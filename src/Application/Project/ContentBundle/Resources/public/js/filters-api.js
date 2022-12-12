$( document ).ready(function() {




    let data = [
        {
            'operador': 'ordenar_por',
            'descricao': 'Ordenar Crescente ou Decrescente',
            'utilizacao': 'campo[ordenar_por]=asc|desc',
            'exemplo': 'nome[ordenar_por]=asc|desc',
        },
        {
            'operador': 'igual',
            'descricao': 'Igualdade',
            'utilizacao': 'campo[igual]=valor',
            'exemplo': 'nome[igual]=maria',
        },
        {
            'operador': 'diferente',
            'descricao': 'Diferença',
            'utilizacao': 'campo[diferente]=valor',
            'exemplo': 'status[diferente]=andamento',
        },
        {
            'operador': 'maior',
            'descricao': 'Maior que',
            'utilizacao': 'campo[maior]=valor',
            'exemplo': 'preco[maior]=10',
        },
        {
            'operador': 'maior_ou_igual',
            'descricao': 'Maior ou igual',
            'utilizacao': 'campo[maior_ou_igual]=valor',
            'exemplo': 'preco[maior_ou_igual]=10',
        },
        {
            'operador': 'menor',
            'descricao': 'Menor que',
            'utilizacao': 'campo[menor]=valor',
            'exemplo': 'estoque[menor]=100',
        },
        {
            'operador': 'menor_ou_igual',
            'descricao': 'Menor ou igual',
            'utilizacao': 'campo[menor_ou_igual]=valor',
            'exemplo': 'estoque[menor_ou_igual]=100',
        },
        {
            'operador': 'nulo',
            'descricao': 'É nulo',
            'utilizacao': 'campo[nulo]',
            'exemplo': 'ativo[nulo]',
        },
        {
            'operador': 'nao_nulo',
            'descricao': 'Não é nulo',
            'utilizacao': 'campo[nao_nulo]',
            'exemplo': 'ativo[nao_nulo]',
        },
        {
            'operador': 'comeca_com',
            'descricao': 'Começa com',
            'utilizacao': 'campo[comeca_com]=valor',
            'exemplo': 'nome[comeca_com]=a',
        },
        {
            'operador': 'termina_com',
            'descricao': 'Termina com',
            'utilizacao': 'campo[termina_com]=valor',
            'exemplo': 'email[termina_com]=@gmail.com',
        },
        {
            'operador': 'contem',
            'descricao': 'Contém',
            'utilizacao': 'campo[contem]=valor',
            'exemplo': 'descricao[contem]=empreendimento',
        },
    ];


    let template = '';



    data.forEach( (element)=>{

        template += `
        <div class="no-margin">
            <div class="operation-tag-content">
                <span>
                  <div class="opblock opblock-post" id="operations-Autenticação-post_api_auth_usuariologin" >
                        
                        
                        <div class="opblock-summary opblock-summary-post">
                          
                            <span class="opblock-summary-method" style="min-width: 140px; background-color: #2283da">
                                ${element.operador}
                            </span>
                            
                            <span class="opblock-summary-path" data-path="/api/usuario/login">
                                <a class="nostyle" href="#/Autenticação/post_api_auth_usuariologin">
                                    <span>
                                        ${element.utilizacao}
                                    </span>
                                 </a>
                            </span>
                            
                            <div class="opblock-summary-description">
                               Exemplo: ${element.exemplo}
                            </div>
                     
                            <button class="authorization__btn unlocked" aria-label="authorization button unlocked">
                                ${element.descricao}
                            </button>
                            
                        </div>
                        
                        
                  </div>
                </span>
            </div>
        </div>
        `;

    });



    let filters = `
<span>
    <div class="opblock-tag-section is-opne" onclick="changeFilters()">
        <h3 class="opblock-tag no-desc" id="operations-tag-Teste" data-tag="Teste" data-is-open="false">
            <a class="nostyle" href="#/Teste">
                <span>Filtros</span>
            </a>
            <small></small>
            <div></div>
            <button aria-expanded="false" class="expand-operation"  title="Expand operation">
                <svg class="arrow" width="20" height="20" aria-hidden="true" focusable="false">
                    <use href="#large-arrow-down" xlink:href="#large-arrow-down"></use>
                </svg>
            </button>
        </h3>
    </div>
</span>

<div class="filters" style="display: block;">
${template}
</div>
<br><br><br><br><br>
`;




    $('.swagger-ui.swagger-container').children().last().html(filters)



});

function changeFilters(){
    let filters = $('.filters')

    if(filters.css('display') === "none"){
        $('.filters').css('display', 'block')
    }else{
        $('.filters').css('display', 'none')
    }
}