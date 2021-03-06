<?php

namespace App\Http\Controllers\Alimento;

use App\Http\Controllers\Controller;
use App\Models\Alimento\Alimento;
use App\Models\Alimento\AlimentoMedidaCaseira;
use App\Models\Alimento\AlimentoReceita;
use App\Models\Grupo\GrupoAlimentar;
use App\Models\Grupo\GrupoPiramide;
use App\Models\Medida\TipoMedidaCaseira;
use App\Models\Medida\UnidadeMedida;
use App\Models\Nutriente\Nutriente;
use App\Models\Nutriente\NutrienteAlimento;
use App\Models\Receita\Receita;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Class AlimentoController
 * @package App\Http\Controllers\Alimento
 */
class AlimentoController extends Controller
{
    /**
     * Mostra uma lista com os alimentos ja cadastrados no banco de dados, junto com as funções
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $alimentos = Alimento::all();
        return view('alimentos.alimentoLista', compact('alimentos'));
    }

    /**
     * Formulário de criação de alimentos.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nutrientes = Nutriente::pluck('nomeNutriente', 'idNutriente');
        foreach ($nutrientes as $index => $nutriente) {
            $aux = Nutriente::where('nomeNutriente', $nutriente)->first()->unidadeMedida->siglaUnidade;
            $nutrientes[$index] = $nutriente . " " . $aux;
        }
        return view('alimentos.alimentosCriacao ', compact('nutrientes'));
    }

    /**
     * Salva as informações básicas de um alimento e redireciona para a criação de medidas caseiras
     * se o alimento a possuir
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validação do cadastro de alimentos
        $this->validate($request,
            [
                'descricaoAlimento' => 'required',
                'idGPiramide' => 'required|numeric',
                'idGAlimentar' => 'required|numeric',
//                'idTACO' => 'numeric',
            ]

        );

        // Criando um alimento
        $alimento = new Alimento();
        $alimento->descricaoAlimento = $request->descricaoAlimento;
        $alimento->grupoPiramide()->associate(GrupoPiramide::find($request->idGPiramide));
        $alimento->grupoAlimentar()->associate(GrupoAlimentar::find($request->idGAlimentar));
//        $alimento->idTACO = $request->idTACO;
        $alimento->save();

        // salvando a imagem
        if ($request->hasFile('image')) {
            $request->image->storeAs('public/alimentos', $alimento->idAlimento . ".png");
        }

        // garantindo a energia = 0 pelo menos
        if (!$request->has('Ntr-1')) {
            $nutrienteAlm = new NutrienteAlimento();
            $nutrienteAlm->alimento()->associate($alimento);
            $nutrienteAlm->idNutriente = 1;
            $nutrienteAlm->qtde = 0;
            $nutrienteAlm->save();
        }
        if (!$request->has('Ntr-2')) {
            $nutrienteAlm = new NutrienteAlimento();
            $nutrienteAlm->alimento()->associate($alimento);
            $nutrienteAlm->idNutriente = 2;
            $nutrienteAlm->qtde = 0;
            $nutrienteAlm->save();
        }

        // Associando os nutrientes com o alimento criado previamente
        if ($request->nutrientes) {
            foreach ($request->nutrientes as $nutriente) {
                $nutrienteAlm = new NutrienteAlimento();
                $nutrienteAlm->alimento()->associate($alimento);
                $nutrienteAlm->idNutriente = $nutriente;
                $nutrienteAlm->qtde = $request['Ntr-' . $nutriente];
                $nutrienteAlm->save();
            }
        }

        // Associando Medidas Caseiras á um Alimento

        if ($request->medidas_caseiras) {
            foreach ($request->medidas_caseiras as $medida_caseira) {
                $alimentoMedidaCaseira = new AlimentoMedidaCaseira();
                $alimentoMedidaCaseira->alimento()->associate($alimento);
                $alimentoMedidaCaseira->idTMCaseira = $medida_caseira;
                $alimentoMedidaCaseira->qtde = $request['Alm-' . $medida_caseira];
                $alimentoMedidaCaseira->tipoUnidade = 2;
                $alimentoMedidaCaseira->save();
            }
        }

        return redirect()->route('alimentos')->with('status', 'Alimento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // lista as medidas caseiras, as quais o alimento possui para serem preenchidas
        $alimento = Alimento::find($id);
        $img = Storage::url("Alimentos/{$id}.png");

        // nutrientes
        $nutrientes = Nutriente::all();
        $nutrienteAlimento = NutrienteAlimento::where('idAlimento', $id)->get();

        $medidasCaseiras = $alimento->alimentoMedidaCaseira;

        // unidade da medida
        $unidade = UnidadeMedida::all();

        return view('alimentos.alimentoComponentes', compact('alimento', 'img', 'nutrientes',
            'nutrienteAlimento', 'unidade', 'medidasCaseiras'));
    }

    /**
     * Carrega a página de edição de alimento
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $alimento = Alimento::find($id);
        $nutriente = new Nutriente();
        $medidaCaseira = new TipoMedidaCaseira();
        $nutrientesAlimento = $alimento->nutrienteAlimento;
        $medidasAlimento = $alimento->alimentoMedidaCaseira;

        $nutrientes = Nutriente::pluck('nomeNutriente', 'idNutriente');
        foreach ($nutrientes as $index => $n) {
            $aux = Nutriente::where('nomeNutriente', $n)->first()->unidadeMedida->siglaUnidade;
            $nutrientes[$index] = $n . " " . $aux;
        }

        // pega todos os nutrientes e medidas que um alimento possui para passar para a view
        $nutrientesContidos = $nutrientesAlimento->map(function ($ntr) {
            return $ntr->idNutriente;
        });

        $medidasContidas = $medidasAlimento->map(function ($mdd) {
            return $mdd->idTMCaseira;
        });

        $nutrientesContidos = $nutrientesContidos->toArray();
        $medidasContidas = $medidasContidas->toArray();

        return view('alimentos.alimentoEditar', compact('alimento', 'nutrientesContidos',
            'medidasContidas', 'nutrientesAlimento', 'medidasAlimento', 'nutriente', 'medidaCaseira', 'nutrientes'));
    }

    /**
     * Atualiza os atributos do alimento
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //validação do cadastro de alimentos
        $this->validate($request,
            [
                'descricaoAlimento' => 'required',
                'idGPiramide' => 'required|numeric',
                'idGAlimentar' => 'required|numeric',
                'idTACO' => 'numeric',
            ]);

        $alimento = Alimento::find($id);
        $alimento->descricaoAlimento = $request->descricaoAlimento;
        $alimento->grupoPiramide()->associate(GrupoPiramide::find($request->idGPiramide));
        $alimento->grupoAlimentar()->associate(GrupoAlimentar::find($request->idGAlimentar));
//        $alimento->idTACO = $request->idTACO;
        $alimento->save();
        // salvando a imagem
        if ($request->hasFile('image')) {
            $request->image->storeAs('public/alimentos', $alimento->idAlimento . ".png");
        }

        // limpando dados antigos
        DB::delete("delete FROM nutrienteAlimento WHERE idAlimento = ?", [$alimento->idAlimento]);
        DB::delete("delete FROM alimento_medidaCaseira WHERE idAlimento = ?", [$alimento->idAlimento]);

        // garantindo a energia = 0 pelo menos
        if (!$request->has('Ntr-1')) {
            $nutrienteAlm = new NutrienteAlimento();
            $nutrienteAlm->alimento()->associate($alimento);
            $nutrienteAlm->idNutriente = 1;
            $nutrienteAlm->qtde = 0;
            $nutrienteAlm->save();
        }
        if (!$request->has('Ntr-2')) {
            $nutrienteAlm = new NutrienteAlimento();
            $nutrienteAlm->alimento()->associate($alimento);
            $nutrienteAlm->idNutriente = 2;
            $nutrienteAlm->qtde = 0;
            $nutrienteAlm->save();
        }

        // Editando um alimento
        foreach ($request->nutrientes as $nutriente) {
            $nutrienteAlm = new NutrienteAlimento();
            $nutrienteAlm->alimento()->associate($alimento);
            $nutrienteAlm->idNutriente = $nutriente;
            $nutrienteAlm->qtde = $request['Ntr-' . $nutriente];
            $nutrienteAlm->save();
        }

        if ($request->medidas_caseiras) {
            foreach ($request->medidas_caseiras as $medida_caseira) {
                $alimentoMedidaCaseira = new AlimentoMedidaCaseira();
                $alimentoMedidaCaseira->alimento()->associate($alimento);
                $alimentoMedidaCaseira->idTMCaseira = $medida_caseira;
                $alimentoMedidaCaseira->qtde = $request['Alm-' . $medida_caseira];
                $alimentoMedidaCaseira->tipoUnidade = 2;
                $alimentoMedidaCaseira->save();
            }
        }

        return redirect()->route('alimentos')->with('status', 'Alimento atualizado com sucesso!');
    }

    /**
     * Deleta um alimento
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Alimento::find($id)->delete();
        return redirect()->route('alimentos')->with('status', 'Alimento removido com sucesso!');
    }

    /**
     * Reativa um alimento, tornando-o possível de ser utilizado nas refeições
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable($id)
    {
        $alimento = Alimento::find($id);
        $alimento->ativoAlimento = 1;
        $alimento->save();
        return redirect()->route('alimentos')->with('status', 'Alimento reativado!');
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable($id)
    {
        // desativando o alimento
        $alimento = Alimento::find($id);
        $alimento->ativoAlimento = 0;
        $alimento->save();

        // desativando as receitas que contém o alimento
        $receitas = AlimentoReceita::where('idAlimento', $id)->get();

        foreach ($receitas as $index => $receita) {
            $receitaDesativada = Receita::find($receita->idReceita);
            $receitaDesativada->ativoReceita = 0;
            $receitaDesativada->save();
        }

        return redirect()->route('alimentos')->with('status', 'Alimento desabilitado!');
    }
}
