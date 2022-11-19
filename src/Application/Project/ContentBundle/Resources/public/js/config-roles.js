
$( document ).ready(function() {

    const Toast = Swal.mixin({
        toast: false,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    // --------------------------------------------------- /

    let url = window.location.href
    let baseUrl = window.location.origin


    let route = false
    if( url.includes('edit') )
        route = 'edit'

    if( url.includes('create') )
        route = 'create'

    if( ( url.includes('create') ||  url.includes('edit') ) && route ) {

        const roles = { adminRoles: [], apiRoles: [], webRoles: []}

        /** Get All Admin Roles  */

        let getRoles = $.get(baseUrl+"/admin/listAllRoles", function( data ) {
            roles['adminRoles'] = data['adminRoles']
            roles['apiRoles'] = data['apiRoles']
            roles['webRoles'] = data['webRoles']
        })

        getRoles.done(function() {

           initChanges(roles, route);

            $(function () { $('[data-toggle="popover"]').popover() })

        }).fail(function() {
            Toast.fire({
                icon: 'error',
                title: 'Erro ao Carregar Permissões',
                html: '<h5>Recarregue a página, caso o erro persista contate o administrador!</h5>'
            })
        })

    }


});

const initChanges = (roles)=> {

    let selectAdmin = $('.div-select-admin-roles')
    let selectApi = $('.div-select-api-roles')
    let selectWeb = $('.div-select-web-roles')

    /** Oculta Div Select*/
    selectAdmin.parent().css('display', 'none')
    selectApi.parent().css('display', 'none')
    selectWeb.parent().css('display', 'none')

    let adminTemplate = '';
    let apiTemplate = '';
    let webTemplate = '';

    let adminRoles = roles['adminRoles']
    let apiRoles = roles['apiRoles']
    let webRoles = roles['webRoles']

    adminRoles.forEach((value, index)=>{
        adminTemplate += generateTemplate(value)
    })

    apiRoles.forEach((value, index)=>{
        apiTemplate += generateTemplate(value)
    })

    webRoles.forEach((value, index)=>{
        webTemplate += generateTemplate(value)
    })

    selectAdmin.parent().parent().append( "<div class='box-body row' style='margin-top: -40px;'>" + adminTemplate + "</div>" )
    selectApi.parent().parent().append( "<div class='box-body row' style='margin-top: -40px;'>" + apiTemplate + "</div>" )
    selectWeb.parent().parent().append( "<div class='box-body row' style='margin-top: -40px;'>" + webTemplate + "</div>" )


    /** Registra alterações dos inputs no select */
    $('.roleCheckBox').on("click", function () {
        let id = this.id
        if ($(this).is(':checked') === true) {
            $(`[value="${id}"]`).prop("selected", "selected");
        } else {
            $(`[value="${id}"]`).removeAttr('selected');
        }
    })

    /** Registar alterações do select nos inputs */
    $('select option').each(function (index, element) {
        let id = this.value
        if ($(this).is(':selected') === true) {
            $(`#${id}`).trigger('click')
        }
    })

    /** Caso tadas as opcoes de um Modulo estivem selecionadas - seleciona o input todas as opcoes */
    $('.div_opcoes').each(function () {

        let id = $(this).attr('id')

        let divInputs = $(`#${id} input`)
        let opcoesCount = 0;
        let numberInputs = divInputs.length -1

        divInputs.each(function () {
            ( $(this).is(':checked') === true ) ? opcoesCount +=1 : '';

            if($(this).attr('class').includes('todas_opcoes')){
                if(numberInputs === opcoesCount){
                    $(this).click()
                }
            }
        })

    })


    $('.todas_opcoes').on("click", function () {
        let id = this.id
        let group = id.replace('TODAS_OPCOES_', '');

        if ($(this).is(':checked') === true) {
            changeAllOptionsGroup(group, true)
        }else{
            changeAllOptionsGroup(group, false)
        }
    })

}

const changeAllOptionsGroup = (group, status) => {

    $(`#OPCOES_${group} input`).each(function (index, element) {
        if(status){
            if ($(this).is(':checked') === false) {
                $(this).click()
            }
        }else{
            if ($(this).is(':checked') === true) {
                $(this).click()
            }
        }
    })
}



const generateTemplate = (group) => {

    //console.log(group)

    let routes = '';

    /** Todas Opções */
    routes += `
    <div>
        <input class="icheckbox_square-blue todas_opcoes" type="checkbox" id="TODAS_OPCOES_${group['title']}">
        <span data-trigger="hover" data-toggle="popover" title="Todas Opções" data-content="Seleciona todas as Permissões do Módulo" style="margin-left: 5px; margin-right: 5px;">
            <i class="fa fa-solid fa-info-circle"></i>
            <label class="form-check-label" for="TODAS_OPCOES_${group['groupName']}">Todas Opções</label>
        </span>
    </div>
`
    group['routes'].forEach((route, index)=>{
        routes += `
    <div>
        <input class="icheckbox_square-blue roleCheckBox" type="checkbox" id="${ route['role'] }">
        <span data-trigger="hover" data-toggle="popover" title="${ route['title'] }" data-content="${ route['description']? route['description'] : '' }" style="margin-left: 5px; margin-right: 5px;">
            <i class="fa fa-solid fa-info-circle"></i>
            <label class="form-check-label" for="${ route['role'] }">${ route['title'] }</label>
        </span>
    </div>
`
    })


    return `
    <div class="col-sm-auto col-md-6 col-lg-3 col-lg-3">
        <div style="padding:20px;">
            <span data-trigger="hover" data-toggle="popover" title="${group['title']}" data-content="${ group['description']? group['description'] : '' }" >
                <label class="control-label">Módulo ${group['title']}</label>
                <i class="fa fa-solid fa-info-circle"></i>
            </span>
            <input type="text" readonly="readonly" class="form-control" value="${group['title']}">
            <label class="control-label" style="padding-top: 10px;">Permissões</label>
            <div class="div_opcoes" id="OPCOES_${group['title']}">
                ${routes}
            </div>
        </div>
    </div>
`;

}


