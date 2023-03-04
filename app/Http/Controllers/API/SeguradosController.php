<?php

use App\Models\Segurados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeguradosController extends Controller
{
    public function index()
    {
        return Segurados::with('enderecos')->get();
    }

    public function create()
    {

    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request['cnpj'] = remove_special_char($request->input('cnpj'));
            $request['cpf'] = remove_special_char($request->input('cpf'));
            $request['telefone'] = remove_especial_char($request->input('telefone'));
            $data_end = $request->enderecos;
            $data = $this->validate($request,[
                'observacao' => 'nullable|string',
                'nome' => 'string|required',
                'tipo' => 'string|required',
                'nome_contato' => 'nullable|string',
                'telefone' => 'nullable|numeric',
                'email' => 'nullable|string|unique:segurados,email',
                'cnpj' => 'sometimes|unique:segurados,cnpj',
                'cpf' => 'sometimes|unique:segurados, cpf',
                'enderecos.logradouro' => 'nullable|string',
                'enderecos.bairro' => 'nullable|string',
                'enderecos.localidade' => 'nullable|string',
                'enderecos.uf' => 'nullable|string',
                'enderecos.numero' => 'nullable|numeric',
                'enderecos.complemento' => 'nullable|string',
                'enderecos.cep' => 'nullable|string',
            ]);
            $segurado = Segurados::create($data);
            if(isArrayNotEmpty($data_end)) {
                $data_end['cep'] = remove_special_char($date_end['cep']);
                $enderco = new EnderecoSegurados();
                $endereco -> fill($data_end);
                $enderco->id_segurados = $segurado->id;
                $endereco->save();
            }
            DB::commit();
            return ['message'=>'sucess'];
        } catch (\Throwable $th){
            DB::rollBack();
            throw $th;
        }
    }

    public function show($id)
    {
    }
    public function update(Request $request, int $id)
    {
       DB::beginTransaction();
       try {
        $segurados = Segurados::findOrFail($id);
        $request['cnpj'] = remove_special_char($request->input('cnpj'));
        $request['cpf'] = remove_special_char($request->input('cpf'));
        $request['telefone'] = remove_special_char($request->input('telefone'));
        $data_end = $request->enderecos;

        unset($request['created_at']);
        unset($request['updated_at']);

        $data = $this->validate($request,[
            'observacao' => 'nullable|string',
            'nome' => 'string|required',
            'tipo' => 'string|required',
            'nome_contato' => 'nullable|string',
            'telefone' => 'nullable|numeric',
            'email' => 'nullable|string|unique:segurados,email,'. $segurados->id,
            'cnpj' => 'sometimes|unique:segurados,cnpj,'. $segurados->id,
            'cpf' => 'sometimes|unique:segurados, cpf,'. $segurado->id,
            'enderecos.logradouro' => 'nullable|string',
            'enderecos.bairro' => 'nullable|string',
            'enderecos.localidade' => 'nullable|string',
            'enderecos.uf' => 'nullable|string',
            'enderecos.numero' => 'nullable|numeric',
            'enderecos.complemento' => 'nullable|string',
            'enderecos.cep' => 'nullable|string',
        ]);
        $segurado->update($data);

        if(isArrayNotEmpty($data_end)) {
            $data_end['cep'] = remove_special_char($data_end['cep']);
            EnderecosSegurados::UpdateOrCreate(
                [
                    'id_segurados' => $segurados -> id
                ],
                [
                    'cep' => $data_end['cep'],
                    'logradouro' => $data_end['logradouro'],
                    'bairro' => $data_end['bairro'],
                    'localidade' => $data_end['localidade'],
                    'uf' => $data_end['uf'],
                    'complemento' => $data_end['complemento'],
                    'numero' => $data_end['numero'],
                ]
                );
        }
        DB::commit();
        return ['message' => 'sucess'];
       } catch (\Throwable $th) {
        DB::rollback();
        throw $th;
       }
    }
    public function destroy($id)
    {
        try {
            $segurados = Segurados::findOrFail($id);
            $segurados->delete();

            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
?>
