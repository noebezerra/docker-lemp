<!DOCTYPE html>
<html lang="pt-br" ng-app="app">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consulta Pontos</title>
  <link rel="manifest" href="manifest.json">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <style>
    body,
    html {
      height: 100%;
    }

    input {
      text-align: center;
    }
  </style>
</head>

<body ng-controller="appCtrl">
  <div class="h-100" ng-if="!filtros.vendedor">
    <div class="container h-100">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="col-12 text-center">
          <span class="fa-stack fa-4x text-info">
            <i class="fas fa-circle fa-stack-2x"></i>
            <i class="fas fa-award fa-stack-1x fa-inverse"></i>
          </span>
          <h1>Ranking de Profissionais</h1>
          <h2 class="text-muted">Consulta Pontos</h2>
          <hr><h6><strong>Informe o CPF do Profissional</strong></h6><hr>
          <input type="number" class="form-control mb-2" id="cpf" ng-model="cpf" autocomplete="off">
          <button type="button" class="btn btn-info btn-block" ng-click="consultar(cpf)"><i class="fas fa-search"></i> Consultar</button>
        </div>
      </div>
    </div>
  </div>
  <div ng-if="filtros.vendedor">
    <header>
      <div class="container">
        <div class="row text-center">
          <div class="col-12">
            <div class="h2">
              <i class="far fa-handshake"></i> Arquitetos <span class="text-muted">vs Produtos</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            <button type="button" class="btn btn-link btn-block" ng-click="show.filtros = !show.filtros">
              <i class="fas" ng-class="{'fa-search-plus': show.filtros, 'fa-search-minus': !show.filtros}"></i> {{!show.filtros ? 'Expandir filtros': 'Esconder filtros'}}
            </button>
          </div>
          <div class="col-4">
            <!-- <button type="button" class="btn btn-link btn-block" ng-click="reseta()">
              <i class="fas fa-power-off"></i>&nbsp;Resetar
            </button> -->
          </div>
          <div class="col-4">
            <a href="index.php" class="btn btn-link btn-block">
              <i class="fas fa-home"></i>&nbsp;Voltar ao Início
            </a>
          </div>
        </div>
      </div>
    </header>
    <div class="container" ng-init="onInit(empresa, filtros.vendedor)">
      <div class="row">
        <!-- filtros -->
        <div class="col-md-3 col-xs-12" ng-show="show.filtros">

          <button class="btn btn-primary btn-block mt-3" ng-class="{'btn-warning': loading}" ng-click="buscar()" ng-disabled="loading">
            <i class="fas fa-sync" ng-class="{'fa-spin': loading}"></i> Buscar
          </button>

          <label><strong>Data ini.</strong></label>
          <input type="date" class="form-control" ng-model="filtros.dtini" ng-disabled="loading">
          <label><strong>Data fim</strong></label>
          <input type="date" class="form-control" ng-model="filtros.dtfim" ng-disabled="loading">

        </div>
        <!-- resultado -->
        <div class="col-xs-12" ng-class="{'col-md-9': show.filtros, 'col-md-12': !show.filtros}">
          <div class="row mt-3">
            <div class="col-xs-12 col-md-6">
              <button class="btn btn-success btn-block" ng-click="gerarPlanilha(result, true)" ng-if="configgrafico.geral.data.length">
                <i class="far fa-file-excel"></i> Exportar todos
              </button>
            </div>
            <div class="col-xs-12 col-md-6">
              <button class="btn btn-success btn-block" ng-click="gerarPlanilha(result, true, true)" ng-if="configgrafico.geral.data.length">
                <i class="far fa-file-excel"></i> Apenas selecionados
              </button>
            </div>
          </div>

          <div class="card mt-1" ng-repeat="(key, r) in result">
            <div class="card-header bg-primary text-light" data-toggle="collapse" href="#collapse-{{explodeName(key)}}">
              <strong class="cursor-pointer">{{key}}</strong>
            </div>
            <div class="collapse multi-collapse" id="collapse-{{explodeName(key)}}">
              <div class="card-body">

                <div class="row">
                  <div class="col-xs-12 col-md-6">
                    <button class="btn btn-success btn-block" ng-click="gerarPlanilha(r)">
                      <i class="far fa-file-excel"></i> Exportar
                    </button>
                  </div>
                  <div class="col-xs-12 col-md-6">
                    <button class="btn btn-success btn-block" ng-click="gerarPlanilha(apenasProdutosSelecionados(r))">
                      <i class="far fa-file-excel"></i> Exportar Selecionados
                    </button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-md-12">
                    <div class="table-responsive">
                      <table class="table table-sm table-striped table-hover table-grid-sales">
                        <thead>
                          <tr>
                            <th>Pedido</th>
                            <th>Documento</th>
                            <th>Produto</th>
                            <th>Cliente</th>
                            <th>CPF</th>
                            <th>Vendedor</th>
                            <th>Qtde</th>
                            <th>{{ filtros.funcao }}</th>
                            <th>Dt. Venda</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr dir-paginate="vendas in r | orderBy: 'SELECIONADO':'desc' | itemsPerPage: 10" current-page="paginaatual" pagination-id="'page'+$index" ng-class="{'text-primary font-weight-bold': vendas.SELECIONADO}">
                            <td>{{ vendas.ID_PEDIDO }}</td>
                            <td>{{ vendas.NRODAV }}</td>
                            <td>
                              {{ vendas.PRODUTODESC }}
                              <small>{{ vendas.PRODUTO }}</small>
                            </td>
                            <td>{{ vendas.NOMECLIENTE }}</td>
                            <td>{{ vendas.CPF }}</td>
                            <td>{{ vendas.NOME }}</td>
                            <td>{{ vendas.QUANTIDADE }}</td>
                            <td>{{ vendas.NOMEFUNCIONARIO }}</td>
                            <td>{{ vendas.DTVENDA }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="text-center">
                      <dir-pagination-controls template-url="includes/dir-pagination/dp-controls-bs4.html" boundary-links="true" direction-links="true" pagination-id="'page'+$index"></dir-pagination-controls>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <div class="card-footer">
              <div class="row align-items-center">
                <div class="col-md-4 col-xs-4 text-center">
                  <p class="m-0">Itens diferentes</p>
                  <p class="m-0"><strong>{{ apenasProdutosSelecionadosUniq(r).length }}</strong></p>
                </div>

                <div class="col-md-4 col-xs-4 text-center">
                  <p class="m-0">Total itens selec.</p>
                  <p class="m-0"><strong>{{ total(apenasProdutosSelecionados(r), 'QUANTIDADE') | number:2}}</strong></p>
                </div>

                <div class="col-md-4 col-xs-4 text-center">
                  <p class="m-0 font-weight-bold">Ponto(s)</p>
                  <p class="m-0">
                    <div class="h5 bg-primary text-white font-weight-bold p-1">{{ total(apenasProdutosSelecionados(r), 'SUBTOTAL') * 0.03 | number:2}}</div>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="js/jquery-3.2.1.js"></script>
  <script type="text/javascript" src="js/popper-1.12.3.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/angular.min.js"></script>
  <script type="text/javascript" src="js/angular-locale_pt-br.js"></script>
  <script type="text/javascript" src="js/ui-bootstrap-tpls-3.0.4.min.js"></script>
  <script type="text/javascript" src="js/lodash.min.js"></script>
  <script type="text/javascript" src="js/moment.min.js"></script>
  <script type="text/javascript" src="js/dirPagination.js"></script>
  <script type="text/javascript" src="js/fusioncharts.js"></script>
  <script type="text/javascript" src="js/fusioncharts.gantt.js"></script>
  <script type="text/javascript" src="js/angular-fusioncharts.min.js"></script>
  <script type="text/javascript" src="js/angular-upload.min.js"></script>
  
  <script type="text/javascript">
    document.querySelector("#cpf").focus();
  </script>

  <script type="text/javascript">
    var app = angular.module('app', ['ui.bootstrap', 'angularUtils.directives.dirPagination', 'lr.upload', 'ng-fusioncharts'])

    app.controller('appCtrl', function($scope, $http, $timeout, upload) {
      $scope.cpf = null
      $scope.empresa = 'p'
      $scope.paginaatual = 1
      $scope.loading = false
      $scope.loadingexportarproduto = false
      $scope.produtosselecionados = []
      $scope.produtosimportados = []
      $scope.configgrafico = {
        geral: {
          chart: {
            caption: "Geral",
            bgColor: "#ffffff",
            showBorder: "0",
            use3DLighting: "0",
            enableSmartLabels: "0",
            showPercentValues: "1",
            showLegend: "1",
            legendShadow: "0",
            legendBorderAlpha: "0",
            defaultcenterlabel: "",
            centerLabel: "$value",
            showTooltip: "0",
            decimals: "2",
            captionFontSize: "14",
            decimalSeparator: ",",
            exportEnabled: "1",
            palettecolors: "f86663,00c7c0"
          },
          data: []
        },
        geralintegrantes: {}
      }
      $scope.show = {
        filtros: true
      }
      $scope.filtros = {
        empresa: null,
        filial: [],
        vendedor: null,
        funcao: 'ARQUITETO POTIGUAR',
        dtini: moment(new Date(new Date().getFullYear(), new Date().getMonth())).subtract(3, 'month').toDate(),
        dtfim: new Date(),
        target: 'produtos',
        produtos: []
      }
      $scope.empresas = [
        { id: 1, name: 'Potiguar', key: 'p' },
        { id: 2, name: 'Terrazoo', key: 't' },
      ]
      $scope.grupos = [
        { name: 'Produto', key: 'PRODUTO' },
        { name: 'Marca', key: 'MARCA' }
      ]
      $scope.filiais = []
      $scope.result = []
      $scope.prodsUpload = {
        onUpload: function(file) {
          $scope.loadingexportarproduto = true
          $scope.produtosimportados = []
        },
        onComplete: function() {
          $scope.loadingexportarproduto = false
        },
        success: function(res) {
          $scope.produtosselecionados = res.data
          $scope.produtosimportados = res.data
          alert('Importação concluída!')
        },
        error: function(error) {
          alert('Error ao fazer importação de produtos!')
        }
      }

      const formatarParaPlanilha = function(itens) {
        const result = _.chain(itens)
          .filter(item => item.ID_PEDIDO)
          .map(item => ({
            ARQUITETO: item.NOMEFUNCIONARIO,
            PEDIDO: item.ID_PEDIDO,
            DAV: item.NRODAV,
            PROD_COD: item.PRODUTO,
            PROD_DESC: item.PRODUTODESC,
            CLIENTE: item.NOMECLIENTE,
            CPF: item.CPF,
            QTDE: item.QUANTIDADE,
            // PRECO_UNIT: item.PRECOUNIT,
            // PRECO_TOTAL: item.SUBTOTAL,
            VENDEDOR: item.NOME,
            DT_VENDA: item.DTVENDA
          }))
          .value()

        return result
      }

      $scope.onInit = function(empresa, vendedor) {
        $scope.filtros.empresa = empresa
        $scope.filtros.vendedor = vendedor
        $scope.filtros.funcao = '0001022020'
        $scope.filtros.produtos = [{
            "PRODUTO": 74187,
            "DESCRICAO": "LUSTRE FINESE G9 TASCHIBRA"
          },
          {
            "PRODUTO": 74624,
            "DESCRICAO": "PENDENTE TD 3010 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74489,
            "DESCRICAO": "PENDENTE TD 3011 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74780,
            "DESCRICAO": "PENDENTE TD 3021 ACOBREADO 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74667,
            "DESCRICAO": "PENDENTE TD 3012 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74616,
            "DESCRICAO": "PENDENTE TD 3008 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74810,
            "DESCRICAO": "PENDENTE TD 3024 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74667,
            "DESCRICAO": "PENDENTE TD 3012 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74616,
            "DESCRICAO": "PENDENTE TD 3008 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74810,
            "DESCRICAO": "PENDENTE TD 3024 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74888,
            "DESCRICAO": "PENDENTE TD 3025 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74799,
            "DESCRICAO": "PENDENTE TD 3022 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 43850,
            "DESCRICAO": "PENDENTE TD 900 1XE27 PTO TASCHIBRA"
          },
          {
            "PRODUTO": 43877,
            "DESCRICAO": "PENDENTE TD 901 1XE27 PTO TASCHIBRA"
          },
          {
            "PRODUTO": 43893,
            "DESCRICAO": "PENDENTE TD 902 1XE27 PT TASCHIBRA"
          },
          {
            "PRODUTO": 74586,
            "DESCRICAO": "PENDENTE TD 3007 CROMADO 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 74683,
            "DESCRICAO": "PENDENTE TD 3013 ACOBREADO 1XE27 TASCHIBRA"
          },
          {
            "PRODUTO": 162825,
            "DESCRICAO": "PLAFON THUNDER 5L GU10 BRONZEARTE"
          },
          {
            "PRODUTO": 163627,
            "DESCRICAO": "PENDENTE AQUARELA CHAPEU CHINES PT BRONZEART"
          },
          {
            "PRODUTO": 163600,
            "DESCRICAO": "PENDENTE AQUARELA CHAPEU CHINES AM BRONZEARTE"
          },
          {
            "PRODUTO": 163368,
            "DESCRICAO": "PENDENTE AQUARELA CHAPEU CHINES AZ BRONZEARTE"
          },
          {
            "PRODUTO": 163384,
            "DESCRICAO": "PENDENTE AQUARELA CHAPEU CHINES BR BRONZEARTE"
          },
          {
            "PRODUTO": 163961,
            "DESCRICAO": "PENDENTE AQUARELA CHAPEU PROLONGADOVM BRONZE"
          },
          {
            "PRODUTO": 162736,
            "DESCRICAO": "PLAFON PIRAMIDAL 6L GU10 BASE QUAD C CRISTAIS"
          },
          {
            "PRODUTO": 106631,
            "DESCRICAO": "PENDENTE URANO ACO COPPER GD 1XE27 AVANT"
          },
          {
            "PRODUTO": 106755,
            "DESCRICAO": "PENDENTE URANO ACO BLACK GD 1XE27 AVANT"
          },
          {
            "PRODUTO": 106534,
            "DESCRICAO": "PENDENTE FARWEST ACO BLACK 3XE27 AVANT"
          },
          {
            "PRODUTO": 252760,
            "DESCRICAO": "PENDENTE LEPHARE GD ALUM PR CE PR 1XE27 AVANT"
          },
          {
            "PRODUTO": 252751,
            "DESCRICAO": "PENDENTE LEPHARE GD ALUM BR CE 1XE27 AVANT"
          },
          {
            "PRODUTO": 252719,
            "DESCRICAO": "PENDENTE BELL GD ALUM PR FO PR 1XE27 AVANT"
          },
          {
            "PRODUTO": 252689,
            "DESCRICAO": "PENDENTE BELL GD ALUM CO BR CE 1XE27 AVANT"
          },
          {
            "PRODUTO": 252670,
            "DESCRICAO": "PENDENTE BELL GD ALUM BR FO OU 1XE27 AVANT"
          },
          {
            "PRODUTO": 105899,
            "DESCRICAO": "PENDENTE ADHARA ACO WHITE 3XE27 AVANT"
          },
          {
            "PRODUTO": 106577,
            "DESCRICAO": "PENDENTE MENKAR ACO WHITE 1XE27 AVANT"
          },
          {
            "PRODUTO": 106569,
            "DESCRICAO": "PENDENTE MENKAR ACO BLACK 1XE27 AVANT"
          },
          {
            "PRODUTO": 106410,
            "DESCRICAO": "PENDENTE TERRA ACO BLACK 1XE27 AVANT"
          },
          {
            "PRODUTO": 106739,
            "DESCRICAO": "PENDENTE URANO ACO BLACK PQ 1XE27 AVANT"
          },
          {
            "PRODUTO": 106623,
            "DESCRICAO": "PENDENTE URANO ACO COPPER PQ 1XE27 AVANT"
          },
          {
            "PRODUTO": 252077,
            "DESCRICAO": "PENDENTE BENJAMI GOLD 5XE27 AVANT"
          },
          {
            "PRODUTO": 106003,
            "DESCRICAO": "PENDENTE MANISA VIDRO AMBER 1XE27 AVANT"
          },
          {
            "PRODUTO": 252735,
            "DESCRICAO": "PENDENTE BELL PQ ALUM CO BR CE 1XE27 AVANT"
          },
          {
            "PRODUTO": 252743,
            "DESCRICAO": "PENDENTE BELL PQ ALUM PR FO PR 1XE27 AVANT"
          },
          {
            "PRODUTO": 252727,
            "DESCRICAO": "PENDENTE BELL PQ ALUM BR FO OU 1XE27 AVANT"
          },
          {
            "PRODUTO": 105961,
            "DESCRICAO": "PENDENTE ISTAMBUL VIDRO AMBER 1XE27 AVANT"
          },
          {
            "PRODUTO": 106062,
            "DESCRICAO": "PENDENTE ANFORA VIDRO GOLD 1XE27 AVANT"
          },
          {
            "PRODUTO": 106135,
            "DESCRICAO": "PENDENTE BELLATRIX VIDRO GOLD 1XE27 AVANT"
          },
          {
            "PRODUTO": 105996,
            "DESCRICAO": "PENDENTE ADANA VIDRO AMBER 1XE27 AVANT"
          },
          {
            "PRODUTO": 106488,
            "DESCRICAO": "PENDENTE KRAS ACO CIMENTO BLACK RED 1XE27 AVA"
          },
          {
            "PRODUTO": 106518,
            "DESCRICAO": "PENDENTE RANA ACO CIMENTO BLACK GREY 1XE27 AV"
          },
          {
            "PRODUTO": 252115,
            "DESCRICAO": "PENDENTE BENJAMIN BLACK 5XE27 AVANT"
          },
          {
            "PRODUTO": 252069,
            "DESCRICAO": "PENDENTE BENJAMIN MIX 5XE27 AVANT"
          },
          {
            "PRODUTO": 106585,
            "DESCRICAO": "PENDENTE MIRA ACO BLACK 1XE27 AVANT"
          },
          {
            "PRODUTO": 105856,
            "DESCRICAO": "PENDENTE ADHARA ACO WHITE 1XE27 AVANT"
          },
          {
            "PRODUTO": 105902,
            "DESCRICAO": "PENDENTE ALDEBARAN ACO BLACK 1XE27 AVANT"
          },
          {
            "PRODUTO": 264458,
            "DESCRICAO": "PENDENTE RUSTICO PRETO FOSCO DI62418 DECOR"
          },
          {
            "PRODUTO": 264210,
            "DESCRICAO": "LUSTRE CLOUD STAR 4XGU10 50W RM1505064 DECOR"
          },
          {
            "PRODUTO": 264466,
            "DESCRICAO": "PENDENTE RUSTICO MADEIRA DI62715 DECOR"
          },
          {
            "PRODUTO": 264121,
            "DESCRICAO": "PENDENTE WISCONSIN 35CM E27X1 PEW003 DECOR"
          },
          {
            "PRODUTO": 264415,
            "DESCRICAO": "PENDENTE RUSTICO CROMADO DI62647 DECOR"
          },
          {
            "PRODUTO": 264113,
            "DESCRICAO": "PENDENTE WISCONSIN 25CM E27X1 PEW003 DECOR"
          },
          {
            "PRODUTO": 203220,
            "DESCRICAO": "PENDENTE TERRACE DOURADO 30CM E27X1 DI51627 D"
          },
          {
            "PRODUTO": 264326,
            "DESCRICAO": "PENDENTE ARAMADO REDONDO DI62555 DECOR"
          },
          {
            "PRODUTO": 264350,
            "DESCRICAO": "PENDENTE DROP M PRETO DI62616 DECOR"
          },
          {
            "PRODUTO": 203955,
            "DESCRICAO": "PENDENTE CALAGRY PTO 20CM E27X1 DI51535 DECOR"
          },
          {
            "PRODUTO": 203998,
            "DESCRICAO": "PENDENTE LACOMBE PTO 25CM DI51542 DECOR"
          },
          {
            "PRODUTO": 264156,
            "DESCRICAO": "PENDENTE GEORGIA BRANCO PEA027 DECOR"
          },
          {
            "PRODUTO": 264148,
            "DESCRICAO": "PENDENTE GEORGIA CAFE E27X1 PEA027 DECOR"
          },
          {
            "PRODUTO": 264474,
            "DESCRICAO": "PENDENTE REDONDO PRETO DI62722 DECOR"
          },
          {
            "PRODUTO": 202932,
            "DESCRICAO": "PENDENTE TERRACE BCO 20CM E27X1 DI51597 DECOR"
          },
          {
            "PRODUTO": 202959,
            "DESCRICAO": "PENDENTE TERRACE DOURADO 20CM E27X1 DI51603 D"
          },
          {
            "PRODUTO": 202185,
            "DESCRICAO": "PENDENTE DUNCAN PTO E27X1 DI51566 DECOR"
          },
          {
            "PRODUTO": 264202,
            "DESCRICAO": "PENDENTE CITRINO E27X1 RM11141P DECOR"
          },
          {
            "PRODUTO": 264180,
            "DESCRICAO": "PENDENTE TURANO E27X1 RM2041PB DECOR"
          },
          {
            "PRODUTO": 264423,
            "DESCRICAO": "PENDENTE RUSTICO DOURADO DI62654 DECOR"
          },
          {
            "PRODUTO": 264431,
            "DESCRICAO": "PENDENTE RUSTICO OURO ROSA DI62661 DECOR"
          },
          {
            "PRODUTO": 264415,
            "DESCRICAO": "PENDENTE RUSTICO CROMADO DI62647 DECOR"
          },
          {
            "PRODUTO": 203947,
            "DESCRICAO": "PENDENTE ALBERTA COBRE E27X1 DI51221 DECOR"
          },
          {
            "PRODUTO": 264105,
            "DESCRICAO": "PENDENTE UTAH E27X1 PEW004 DECOR"
          },
          {
            "PRODUTO": 264458,
            "DESCRICAO": "PENDENTE RUSTICO PRETO FOSCO DI62418 DECOR"
          },
          {
            "PRODUTO": 234800,
            "DESCRICAO": "PENDENTE SUKA SD8317 STELLA"
          },
          {
            "PRODUTO": 66958,
            "DESCRICAO": "CARRARA BACIA P CAIXA ACOP MARRON P606 22"
          },
          {
            "PRODUTO": 66940,
            "DESCRICAO": "CAIXA ACOP DUALF CD11F 22 MARROM CARR/NUOVA"
          },
          {
            "PRODUTO": 42897,
            "DESCRICAO": "ASSENTO POLIEST SLOW CLOSE AP 237 MARROM DECA"
          },
          {
            "PRODUTO": 62138,
            "DESCRICAO": "CARRARA BACIA P CAIXA ACOP GELO P606 17"
          },
          {
            "PRODUTO": 60526,
            "DESCRICAO": "CAIXA ACOP DUALF CD11F 17 GELO CARR/NUO"
          },
          {
            "PRODUTO": 614300,
            "DESCRICAO": "ASSENTO LINK GELO AP 23 17 DECA"
          },
          {
            "PRODUTO": 866830,
            "DESCRICAO": "PIANO BACIA P CAIXA ACOP GELO P330 17 DECA"
          },
          {
            "PRODUTO": 62065,
            "DESCRICAO": "CAIXA ACOP CD21F GELO POLO/PIANO/QUADRA/AXIS"
          },
          {
            "PRODUTO": 193895,
            "DESCRICAO": "ASSENTO SLOWCLOSE TERM QUAD/PIANO GELO AP336"
          },
          {
            "PRODUTO": 67628,
            "DESCRICAO": "CUBA L 737 22 APOIO QUAD C MESA MARRON FOSCO"
          },
          {
            "PRODUTO": 866962,
            "DESCRICAO": "CUBA L 131C SOB RET 605X475MM GEL0 17 C GREL"
          },
          {
            "PRODUTO": 58823,
            "DESCRICAO": "CUBA L 94 ESPECIAL CONC GELO 17"
          },
          {
            "PRODUTO": 708240,
            "DESCRICAO": "UNIC TORNEIRA LAVAT MESA 1197 C90 CR"
          },
          {
            "PRODUTO": 801135,
            "DESCRICAO": "UNIC DUCHA 1984 C90 ACT CR"
          },
          {
            "PRODUTO": 806242,
            "DESCRICAO": "UNIC TORNEIRA LAVAT MESA 1189 C90 CR"
          },
          {
            "PRODUTO": 802867,
            "DESCRICAO": "UNIC MIST P LAVAT 1875 C90 CR"
          },
          {
            "PRODUTO": 802700,
            "DESCRICAO": "DREAM MIST LAV MESA 1877 C87 CR"
          },
          {
            "PRODUTO": 612928,
            "DESCRICAO": "GOURMET MIST MONOC COZ MESA 2280C BM CR"
          },
          {
            "PRODUTO": 67652,
            "DESCRICAO": "SPIN GOURMET MONOC COZ MESA 2281 C DECA"
          },
          {
            "PRODUTO": 220639,
            "DESCRICAO": "PORCEL 120X120 BIANCO GIOIA POL RET BIANCOGRE"
          },
          {
            "PRODUTO": 266272,
            "DESCRICAO": "PORCEL 120X120 CREMA VALENCA BIANCO POL DECO"
          },
          {
            "PRODUTO": 266140,
            "DESCRICAO": "PORCEL 100X100 SUPER BRANCO POL DECOR"
          },
          {
            "PRODUTO": 266264,
            "DESCRICAO": "PORCEL 100X100 MONO BIANCO POL DECOR"
          },
          {
            "PRODUTO": 266159,
            "DESCRICAO": "PORCEL 100X100 CARRARA BRANCO POL DECOR"
          },
          {
            "PRODUTO": 266191,
            "DESCRICAO": "PORCEL 90X90 CALACAT BRANCO POL DECOR"
          },
          {
            "PRODUTO": 266230,
            "DESCRICAO": "PORCEL 90X90 ROMANI CINZA POL DECOR"
          },
          {
            "PRODUTO": 266221,
            "DESCRICAO": "PORCEL 90X90 ROMANI BEIGE POL DECOR"
          },
          {
            "PRODUTO": 266205,
            "DESCRICAO": "PORCEL 90X90 LUCCA BRANCO POL DECOR"
          },
          {
            "PRODUTO": 266183,
            "DESCRICAO": "PORCEL 60X120 CALACAT BRANCO POL DECOR"
          },
          {
            "PRODUTO": 149691,
            "DESCRICAO": "PORCEL 90X180 CALACATTA MARMORE DECOR"
          },
          {
            "PRODUTO": 149675,
            "DESCRICAO": "PORCEL 90X180 BIANCO ABSOLUTE DECOR"
          },
          {
            "PRODUTO": 313971,
            "DESCRICAO": "PORCEL 60X120 CALACATA MC LAMINA INCEPA"
          },
          {
            "PRODUTO": 246328,
            "DESCRICAO": "PORCEL 120X120 PIERRE BELLE CREME POL PORTOBE"
          },
          {
            "PRODUTO": 246310,
            "DESCRICAO": "PORCEL 120X120 PIERRE BELLE BLANC POL PORTOBE"
          },
          {
            "PRODUTO": 847623,
            "DESCRICAO": "PORCEL 90X90 BIANCO CARRARA POL RET PORTO "
          },
          {
            "PRODUTO": 85286,
            "DESCRICAO": "PORCEL 90X90 GALILEU CREMA POL PORTOBELLO "
          },
          {
            "PRODUTO": 54291,
            "DESCRICAO": "PORCEL 60X120 VERT ST PAUL POL PORTOBELLO"
          },
          {
            "PRODUTO": 165190,
            "DESCRICAO": "REVEST 20X20 ALGARVE MIX RET PORTOBELLO"
          },
          {
            "PRODUTO": 279218,
            "DESCRICAO": "PORCEL 100X100 PULPIS BEGE ACET RET CEUSA"
          },
          {
            "PRODUTO": 279226,
            "DESCRICAO": "PORCEL 100X100 PULPIS BEGE POL RET CEUSA"
          },
          {
            "PRODUTO": 280143,
            "DESCRICAO": "PORCEL 100X100 TREVO BETON 4397 CEUSA"
          },
          {
            "PRODUTO": 231916,
            "DESCRICAO": "REVEST 32X100 TRAMA SAHARA ACET RET 4335 CEUS"
          },
          {
            "PRODUTO": 231908,
            "DESCRICAO": "REVEST 32X100 TRAMA RIMINI ACET RET 4334 CEUS"
          },
          {
            "PRODUTO": 279250,
            "DESCRICAO": "REVEST 32X100 LEQUE CIMENTO ACET RET CEUSA"
          },
          {
            "PRODUTO": 279242,
            "DESCRICAO": "REVEST 32X100 LEQUE NEON ACET RET CEUSA"
          },
          {
            "PRODUTO": 279234,
            "DESCRICAO": "REVEST 32X100 LEQUE BRANCO ACET RET CEUSA RE"
          },
          {
            "PRODUTO": 231878,
            "DESCRICAO": "REVEST 43,2X91 TRAVERTINO SOIE BRIL RET 2959"
          },
          {
            "PRODUTO": 863513,
            "DESCRICAO": "REVEST 43,2X91 REF 2975 NUANCE ONDAS BRIL CEU"
          },
          {
            "PRODUTO": 231819,
            "DESCRICAO": "REVEST 43,2X91 CANELADO BCO BRIL RET 2950 CE"
          },
          {
            "PRODUTO": 231860,
            "DESCRICAO": "REVEST 43,2X91 CANELADO PTO BRIL RET 2951"
          },
          {
            "PRODUTO": 231827,
            "DESCRICAO": "REVEST 43,2X91 CANELADO ROSA BRIL RET 2957"
          },
          {
            "PRODUTO": 231622,
            "DESCRICAO": "REVEST 43,2X91 CANELADO CINZA BRIL RET 2958"
          },
          {
            "PRODUTO": 36889,
            "DESCRICAO": "REVEST 43,2X91 NOR PIRAMIDE ACET 2940 CEUSA"
          },
          {
            "PRODUTO": 36897,
            "DESCRICAO": "REVEST 43,2X91 NOR LISO ACET 2630 CEUSA"
          },
          {
            "PRODUTO": 45721,
            "DESCRICAO": "REVEST 43,2X91 GOLDEN BRANCO RET 2489 CEUSA"
          },
          {
            "PRODUTO": 863505,
            "DESCRICAO": "REVEST 43,2X91 REF 2976 NUANCE PIRAMIDE BRIL"
          },
          {
            "PRODUTO": 279269,
            "DESCRICAO": "REVEST 43,2X91 JOAQUINA BRIL ACET RET CEUSA"
          },
          {
            "PRODUTO": 279277,
            "DESCRICAO": "REVEST 43,2X91 JOAQUINA NEON ROSA BRIL ACET R"
          },
          {
            "PRODUTO": 280127,
            "DESCRICAO": "REVEST 45X65,5 COLHER DE SOPA 8400 CEUSA"
          },
          {
            "PRODUTO": 279153,
            "DESCRICAO": "REVEST 43,7X63,1 ARCOS CHUMBO ACET RET CEUSA"
          },
          {
            "PRODUTO": 231754,
            "DESCRICAO": "REVEST 43,7X63,1 CANUDOS GRAF ACET RET 8448"
          },
          {
            "PRODUTO": 384453,
            "DESCRICAO": "REVEST 43,7X63,1 REF 8198 FILETADO STONE"
          },
          {
            "PRODUTO": 45918,
            "DESCRICAO": "REVEST 43,7X63,1 CAMADA FENDI 8440 UNIQUE"
          },
          {
            "PRODUTO": 279285,
            "DESCRICAO": "REVEST 58X58 DRAPEADO BRANCO ACET RET CEUSA"
          },
          {
            "PRODUTO": 280135,
            "DESCRICAO": "REVEST 58X58 DRAPEADO CORTEN 6073 CEUSA"
          },
          {
            "PRODUTO": 279293,
            "DESCRICAO": "REVEST 58X58 DRAPEADO BETON ACET RET CEUSA"
          },
          {
            "PRODUTO": 280151,
            "DESCRICAO": "REVEST 28,8X1,19 BOTECO LAPA 8706 CEUSA"
          },
          {
            "PRODUTO": 79499,
            "DESCRICAO": "REVEST 28,8X1,19 BRISE CONCRETO ACET RET 8115"
          },
          {
            "PRODUTO": 281280,
            "DESCRICAO": "PORCEL 28,8X119 REF 8140 DEMOLICAO CEUSA"
          },
          {
            "PRODUTO": 847135,
            "DESCRICAO": "PORCEL 28,8X119 REF 8146 TULHA CEUSA"
          },
          {
            "PRODUTO": 279161,
            "DESCRICAO": "REVEST 28,8X119 REFUGIO ACET RET CEUSA"
          },
          {
            "PRODUTO": 384429,
            "DESCRICAO": "REVEST 33,8X64,3 COMPOSE BRIL 2854 CEUSA"
          },
          {
            "PRODUTO": 226394,
            "DESCRICAO": "REVEST 61X91 LAD COLONIAL 8088 CEUSA"
          },
          {
            "PRODUTO": 36862,
            "DESCRICAO": "PORCEL 80X80 BORGHINI POL 8917 CEUSA"
          },
          {
            "PRODUTO": 296368,
            "DESCRICAO": "PORCEL 106,5X106,5 MARMO DORO POL VILLAGRES"
          },
          {
            "PRODUTO": 296341,
            "DESCRICAO": "PORCEL 106,5X106,5 ARTICO POL VILLAGRES"
          },
          {
            "PRODUTO": 296350,
            "DESCRICAO": "PORCEL 106,5X106,5 ARTICO ACET VILLAGRES"
          },
          {
            "PRODUTO": 296325,
            "DESCRICAO": "PORCEL 108X108 UNIQUE GRAFITE ACET VILLAGRES"
          },
          {
            "PRODUTO": 290025,
            "DESCRICAO": "PORCEL 90,5X90,5 ANTIQUE OFF WHITE POL VILLAG"
          },
          {
            "PRODUTO": 192961,
            "DESCRICAO": "PORCEL 61X106,5 MARMO D ORO BCO POL VILLAGRES"
          },
          {
            "PRODUTO": 193569,
            "DESCRICAO": "PORCEL 61X106,5 LITHOS POL VILLAGRES"
          },
          {
            "PRODUTO": 297569,
            "DESCRICAO": "PORCEL 26,5X138 BARONESA IPE ACET VILLAGRES"
          },
          {
            "PRODUTO": 297534,
            "DESCRICAO": "PORCEL 26,5X138 PROVENCE GRAN VILLAGRES"
          },
          {
            "PRODUTO": 297542,
            "DESCRICAO": "PORCEL 26,5X138 CAMPESTRE GRAN VILLAGRES"
          },
          {
            "PRODUTO": 297550,
            "DESCRICAO": "PORCEL 26,5X138 MOGNO ACET VILLAGRES"
          },
          {
            "PRODUTO": 249866,
            "DESCRICAO": "REVEST 25X25 CARIBBEAN BRIL VILLAGRES"
          },
          {
            "PRODUTO": 283312,
            "DESCRICAO": "PASTILHA 2X2 AUTOADESIVA MADREPEROLA NATURAL"
          },
          {
            "PRODUTO": 867640,
            "DESCRICAO": "PASTILHA 1,5X1,5 CRISTAL ST MIX ST DOURADO 1P"
          },
          {
            "PRODUTO": 867594,
            "DESCRICAO": "PASTILHA 1,5X1,5 CRISTAL ST MIX ST BEGE 1PC"
          },
          {
            "PRODUTO": 867624,
            "DESCRICAO": "PASTILHA 1,5X1,5 CRISTAL ST MIX ST BEGE ESC 1"
          },
          {
            "PRODUTO": 45071,
            "DESCRICAO": "PASTILHA 1,5X1,5 GRIETA METAL FENDI VETROMANI"
          },
          {
            "PRODUTO": 283509,
            "DESCRICAO": "PASTILHA 1,5X1,5 DIAMOND STONE MIX BEGE 1PC"
          },
          {
            "PRODUTO": 283495,
            "DESCRICAO": "PASTILHA 1,5X1,5 GRIETA METAL MIX AZUL 1P"
          },
          {
            "PRODUTO": 301019,
            "DESCRICAO": "PASTILHA 1,5X1,5 GLASS STONE 1PC GS110 GLASS"
          },
          {
            "PRODUTO": 300918,
            "DESCRICAO": "PASTILHA 7,5X15 MIRROR BRICK SILVER MBM01 1PC"
          },
          {
            "PRODUTO": 300926,
            "DESCRICAO": "PASTILHA 7,5X15 MIRROR BRICK BROWN MBM02 1PC"
          }
        ]
        $scope.produtosselecionados = $scope.filtros.produtos.map(item => item.PRODUTO)
        this.onEmpresaChange()
        // if (vendedor) $timeout(() => $scope.buscar(), 1000);
      }

      $scope.reseta = function() {
        $scope.paginaatual = 1
        // $scope.produtosselecionados = []
        $scope.produtosimportados = []
        $scope.filiais = []
        $scope.result = []
        $scope.configgrafico = {
          geral: {
            chart: {
              caption: "Geral",
              bgColor: "#ffffff",
              showBorder: "0",
              use3DLighting: "0",
              enableSmartLabels: "0",
              showPercentValues: "1",
              showLegend: "1",
              legendShadow: "0",
              legendBorderAlpha: "0",
              defaultcenterlabel: "",
              centerLabel: "$label: $value",
              showTooltip: "0",
              decimals: "2",
              captionFontSize: "14",
              decimalSeparator: ",",
              exportEnabled: "1",
              palettecolors: "f86663,00c7c0"
            },
            data: []
          },
          geralintegrantes: {}
        }
        $scope.filtros = {
          empresa: $scope.filtros.empresa,
          filial: [],
          vendedor: $scope.filtros.vendedor,
          // funcao: 'ARQUITETO POTIGUAR',
          dtini: moment(new Date(new Date().getFullYear(), new Date().getMonth())).subtract(3, 'month').toDate(),
          dtfim: new Date(),
          target: 'produtos',
          produtos: []
        }
        $scope.onInit($scope.filtros.empresa, $scope.filtros.vendedor)
      }

      $scope.onEmpresaChange = function() {
        getFiliais()
      }

      $scope.buscar = function() {
        $scope.loading = true
        let { empresa, filial, dtini, dtfim, funcao, vendedor } = $scope.filtros
        $http({
          url: 'http://localhost/api',
          method: 'get',
          params: { 
            app_id: '862ca3b3c26992fb74377261e2692d41',
            mode: 'vendasporfuncao', empresa, dtini, dtfim, funcao, vendedor 
          }
        })
        .then(res => {
          if (res.data.flag) {
            let total = res.data.data.length
            let data = $scope.produtosSelecionados(res.data.data)
            data = $scope.onAgrupamento(data)
            $scope.result = $scope.ordenacao(data)

            // grafico geral
            $scope.configgrafico.geral.data = [{
                label: 'Não selecionados',
                value: res.data.data.filter(o => !o.SELECIONADO).length
              },
              {
                label: 'Selecionados',
                value: res.data.data.filter(o => o.SELECIONADO).length
              }
            ]
            $scope.configgrafico.geral.chart.defaultcenterlabel = total.toString()

            // grafico com todos integrantes
            let keys = Object.keys($scope.result)
            let geralintegrantes = []
            keys.forEach(e => {
              const nomearr = e.split(' ')
              const nomeprofissional = `${nomearr[0]} ${nomearr[nomearr.length - 1]}`

              // criando grafico por pessoa
              geralintegrantes.push({
                label: nomeprofissional,
                value: $scope.result[e].filter(o => o.SELECIONADO).length
              })

              // dados por pessoa
              $scope.configgrafico[e] = {
                chart: angular.copy($scope.configgrafico.geral.chart),
                data: []
              }
              $scope.configgrafico[e].chart.caption = ''
              $scope.configgrafico[e].data.push({
                label: 'Não selecionados',
                value: $scope.result[e].filter(o => !o.SELECIONADO).length
              })
              $scope.configgrafico[e].data.push({
                label: 'Selecionados',
                value: $scope.result[e].filter(o => o.SELECIONADO).length
              })
              $scope.configgrafico[e].chart.defaultcenterlabel = $scope.total($scope.configgrafico[e].data, 'value').toString()

            })
            $scope.configgrafico.geralintegrantes = {
              chart: angular.copy($scope.configgrafico.geral.chart),
              data: geralintegrantes
            }
            $scope.configgrafico.geralintegrantes.chart.caption = 'Por funcionário'
            $scope.configgrafico.geralintegrantes.chart.palettecolors = ''
            $scope.configgrafico.geralintegrantes.chart.defaultcenterlabel = $scope.total(geralintegrantes, 'value').toString()
          }
          $scope.loading = false
        })
        .catch(err => {
          console.error('Servidor indisponível')
          $scope.loading = false
        })
      }

      $scope.onEmpresaChange = function() {
        getFiliais()
      }

      $scope.onAgrupamento = function(arr) {
        return _.groupBy(arr, 'NOMEFUNCIONARIO')
      }

      $scope.produtosSelecionados = function(produtos) {
        produtos.map(produto => {
          console.log('produto:', produto.PRODUTO);
          let encontrado = [...$scope.produtosselecionados].includes(produto.PRODUTO)
          produto.SELECIONADO = encontrado
        })
        return produtos
      }

      $scope.apenasProdutosSelecionadosUniq = function(produtos) {
        return _.uniqBy(produtos.filter(o => o.SELECIONADO), 'PRODUTO')
      }

      $scope.apenasProdutosSelecionados = function(produtos) {
        return produtos.filter(o => o.SELECIONADO)
      }

      $scope.explodeName = function(name) {
        return name.split(' ').join('-')
      }

      $scope.getProducts = function(val) {
        return $http.get('wsmdlprd.php', {
          params: {
            produto: val,
            empresa: $scope.filtros.empresa,
            limite: 10
          }
        }).then(function(response) {
          return response.data.map(function(item) {
            return {
              codigo: item.codigo,
              descricao: item.descricao,
              label: item.codigo + ' ' + item.descricao + ' (' + item.ean + ')'
            }
          })
        })
      }

      $scope.gerarPlanilha = function(dados, todos = false, apenasSelec = false) {
        if (todos) {
          dados = _.chain(dados).values().flatten().value()
        }

        if (apenasSelec) {
          dados = $scope.apenasProdutosSelecionados(dados)
        }

        if (!dados.length) return alert('Não há dados para exportar!')

        const data = formatarParaPlanilha(dados)

        $http.post('report.xls.php', {
            data
          })
          .then(
            res => {
              if (res.data) window.open(res.data.file, '_blank')
            },
            error => {
              alert('Erro gerando planilha, contate suporte técnico!')
              console.error(error)
            }
          )
      }

      $scope.pset = function(item, model, label, event) {
        var input = $('#psearch')
        if (item) {
          $scope.filtros.produtos.push(item)
          $scope.produtosselecionados.push(item.codigo)
          $timeout(function() {
            input.val('')
            input.focus()
          }, 100)
        }
      }

      $scope.premove = function(index) {
        $scope.filtros.produtos.splice(index, 1)
      }

      $scope.ordenacao = function(obj) {
        let result = {}
        let ord = _.orderBy(obj, (val) => {
          return val.filter(o => o.SELECIONADO).length
        }, ['desc'])
        ord.forEach(e => {
          result[e[0].NOMEFUNCIONARIO] = e
        });
        return result
      }

      $scope.total = function(arr, propriedade) {
        return arr.reduce((acc, cur) => {
          return acc + cur[propriedade]
        }, 0)
      }

      const getFiliais = function() {
        const needle = $scope.empresas.find(empresa => empresa.key == $scope.filtros.empresa);
        $scope.filiais = []
        $http({
          url: 'http://localhost/api',
          method: 'get',
          params: {
            app_id: '862ca3b3c26992fb74377261e2692d41',
            mode: 'filiais', empresa: needle.id
          }
        }).then(function(res) {
          if (res.data.flag) {
            $scope.filiais = res.data.data.map(o => {
              return { codigo: o.loja, name: o.name }
            })
          }
        }, function(err) {
          console.log(err);
          alert('Servidor indisponível');
        });
      }

      $scope.consultar = (cpf) => {
        $http({
          url: 'http://localhost/api',
          method: 'get',
          params: {
            app_id: '862ca3b3c26992fb74377261e2692d41',
            mode: 'buscaClientePorCpfFone', cpf
          }
        }).then(function(res) {
          if (res.data.flag) {
            $scope.filtros.vendedor = res.data.data
            $timeout(() => $scope.buscar(), 500);
          } else {
            alert('CPF não encontrado')
          }
        }, function(err) {
          console.log(err);
          alert('Servidor indisponível');
        });
      }

    });
  </script>

</body>

</html>