let selectedElement;
let cameraList = [];
let selectedCamera = {video: true, audio:false};
let cameraToSwap = {video: true, audio:false};

let hasCameraChanged = false;
let currentStream = null;

let activeScheduleColumnsSearch = [];
let inactiveScheduleColumnsSearch = [];

let activeColumnsTemp = [];
let inactiveColumnsTemp = [];

const dt = new DataTransfer();

const PROGRESS_TIME = 30000;

let automatedTimeIsOn = true;

let idInterval;

let driversToSearch = [];
let employessToSearch = [];
let isDriversList = true;

let employessRecordList = [];
let driversRecordList = [];

function dateTimeMask(value) {
    let x = value.replace(/\D+/g, '').match(/(\d{0,2})(\d{0,2})(\d{0,4})(\d{0,2})(\d{0,2})(\d{0,2})/);
    return !x[2] ? x[1] : `${x[1]}/${x[2]}` + (!x[3] ? `` : `/${x[3]}` + ` `) + (!x[4] ? `` : x[4]) + (!x[5] ? `` : `:${x[5]}`) + (!x[6] ? `` : `:${x[6]}`);   
}

jQuery(function($){
    var bindDatePicker = function() {
         $(".date").datetimepicker({
         format:'DD/MM/YYYY hh:mm:ss',
             icons: {
                 time: "fa fa-clock-o",
                 date: "fa fa-calendar",
                 up: "fa fa-arrow-up",
                 down: "fa fa-arrow-down"
             }
         }).find('input:first').on("blur",function () {
             var date = parseDate($(this).val());
 
             if (! isValidDate(date)) {
                 date = moment().format('YYYY-MM-DD');
             }
 
             $(this).val(date);
         });
     }
    
    var isValidDate = function(value, format) {
         format = format || false;
         if (format) {
             value = parseDate(value);
         }
 
         var timestamp = Date.parse(value);
 
         return isNaN(timestamp) == false;
    }
    
    var parseDate = function(value) {
         var m = value.match(/^(\d{1,2})(\/|-)?(\d{1,2})(\/|-)?(\d{4})$/);
         if (m)
             value = m[5] + '-' + ("00" + m[3]).slice(-2) + '-' + ("00" + m[1]).slice(-2);
 
         return value;
    }
    
    bindDatePicker();

    
});

const init = () =>{
    
    getDrivers();
    getEmployess();
    manageListAccess();
    progressTimer();
    setTableLength(50);

}

const cpfMask = (element) => {

    let cpfValue = element.value;

    if( !cpfValue.match(/^[0-9]/)) return;

    cpfValue = cpfValue.replace(/\D/g,"")
    cpfValue = cpfValue.replace(/(\d{3})(\d)/,"$1.$2")
    cpfValue = cpfValue.replace(/(\d{3})(\d)/,"$1.$2")
    cpfValue = cpfValue.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
    element.value = cpfValue;
}

const plateMask = (event, element) => {
    let plate = element.value; 

    if(event.keyCode === 8) return;
 
    if (plate.length === 3){                                                       
        plate += "-";
        element.value = plate; 
    }
    
}

const phoneMask = (event, element) => {

    if(event.keyCode === 8) return;

    let phoneValue = element.value;

    phoneValue = phoneValue.replace(/\D/g,"")

    if( !phoneValue.match(/^[0-9]/)) return;

    if(phoneValue.length === 1) {
        element.value = `(${phoneValue}`;
        return;
    }

    let x = '';

    if(phoneValue.length <= 10){
        x = phoneValue.replace(/\D+/g, '').match(/(\d{0,2})(\d{0,4})(\d{0,4})/);
    }else{
        x = phoneValue.replace(/\D+/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    }

    element.value = !x[2] ? `(${x[1]})` : `(${x[1]}) ${x[2]}` + (!x[3] ? `` : `-${x[3]}`);   
}

const errorReportValidate = (action) => {

    if(action != 'edit'){
        const attachment = document.getElementById('attachment').value;
        
        if(!attachment) {
            alert('Favor anexar uma evidência!');
            return false;
        }
    }    

    return validateEmail();
}

const validateEmail = () =>{

    const mail = document.getElementById('email').value;
    const feedback = document.getElementById('mail-feedback');
    const mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    
    if(mail.match(mailformat)){
        feedback.style.display = 'none';
        return true;

    } else {
        feedback.style.display = 'block';
        return false;
    }
}

const editTruckType = (id, description) => {

    document.getElementById('id').value = id;
    document.getElementById('description').value = description;
    document.getElementById('action').value = 'edit';
    document.getElementById('title').innerHTML = 'Tipo de Veículo - Editar';
}

const resetNewTruck = () =>{
    document.getElementById('title').innerHTML = 'Tipo de Veículo - Novo';
    document.getElementById('id').value = null;
    document.getElementById('action').value = 'save';
}

const editForm = (id, name) => {

    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('action').value = 'edit';
    document.getElementById('title').innerHTML = 'Editar';
}

const resetForm = () =>{
    document.getElementById('title').innerHTML = 'Criar';
    document.getElementById('id').value = null;
    document.getElementById('action').value = 'save';
}

const editShippingCompany = (id, name) => {

    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('action').value = 'edit';
    document.getElementById('title').innerHTML = 'Transportadora - Editar';
}

const resetShippingCompany = () =>{
    document.getElementById('title').innerHTML = 'Transportadora - Novo';
    document.getElementById('id').value = null;
    document.getElementById('action').value = 'save';
}

const validateStatus = () => {

    document.getElementById('btn-salvar').disabled = true;

    const operationExit = document.getElementById('operationExit').value;

    const arrival = document.getElementById('arrival').value;

    if(!arrival && !operationExit) {
        document.getElementById('scheduleStatus').value = 'Agendado';
        return true;
    }

    const operationStart = document.getElementById('operationStart').value;

    if(!operationStart && !operationExit) {
        document.getElementById('scheduleStatus').value = 'Aguardando';
        return true;
    }

    const operationDone = document.getElementById('operationDone').value;

    if(!operationDone && !operationExit) {
        document.getElementById('scheduleStatus').value = 'Em operação';
        return true;
    }

    if(!operationExit) {
        document.getElementById('scheduleStatus').value = 'Fim de operação';
        return true;
    }
    else{

        const result = validationFields();

        if(result){
            document.getElementById('scheduleStatus').value = 'Liberado';
            return result;
        }

        return result;
    }
}

const validationFields = () => {

    const fields = [
        {'name':'operationScheduleTime', 'label': 'Agendamento'},
        {'name':'arrival', 'label': 'Chegada'},
        {'name':'operationStart', 'label': 'Início'},
        {'name':'operationDone', 'label': 'Fim'},
        {'name':'operationExit', 'label': 'Saída'},
        {'name':'driverName', 'label': 'Nome Motorista'},
        {'name':'cpf', 'label': 'CPF'},
        {'name':'operationType', 'label': 'Operação'},
        {'name':'shippingCompany', 'label': 'Transportadora'},
        {'name':'city', 'label': 'Cidade'},
        {'name':'binSeparation', 'label': 'Separação BIN'},
        {'name':'shipmentId', 'label': 'Shipment ID'},
        {'name':'dock', 'label': 'Doca'},
        {'name':'truckType', 'label': 'Tipo Veículo'},
        {'name':'licenceTruck', 'label': 'Placa Cavalo'},
        {'name':'licenceTrailer', 'label': 'Placa carreta'},
        {'name':'licenceTrailer2', 'label': 'Placa Carreta 2'},
        {'name':'dos', 'label': 'DOs'},
        {'name':'invoice', 'label': 'NF'},
        {'name':'grossWeight', 'label': 'Peso Final'},
        {'name':'pallets', 'label': 'Paletes'},
        {'name':'material', 'label': 'Material'},
        {'name':'observation', 'label': 'Observação'}
    ];

    let isValid = true;

    for (const field of fields) {

        const element = document.getElementById(field.name).value.toString();

        if(element) continue;

        isValid = false;
        document.getElementById('btn-salvar').disabled = false;

        customAlert('alert-danger', `Favor preencher o campo ${field.label}`);
        break;
    }

    return isValid;
}

const dateTimeHandleBlur = (element) => {

    const dateTimeValue = element.value;

    if(element.value == '') {
        setTimeout(() => {
            element.innerHTML = '';
            element.value = '';
        }, 10);
    }else{
        setTimeout(() => {
            element.innerHTML = dateTimeValue;
            element.value = dateTimeValue;
            dateTimeHandleKeyUp(element);
        }, 10);
    }
}

const dateTimeHandleKeyUp = (element) => {

    let dateTimeValue = element.value;

    dateTimeValue = dateTimeValue.replace(/[a-zA-Z]/g, '');

    dateTimeValue = dateTimeMask(dateTimeValue);

    element.innerHTML = dateTimeValue;
    element.value = dateTimeValue;
}

const handleShowMenu = () => {
    document.getElementById('menu-nav-bar').style.display = 'block';
}

const handleHideMenu = () => {
    document.getElementById('menu-nav-bar').style.display = 'none';
}

const readColumns = () => {

    document.querySelectorAll('div[name="active-column-name"]').forEach(element => {
        activeColumnsTemp.push(element);
    });

    document.querySelectorAll('div[name="inactive-column-name"]').forEach(element => {
        inactiveColumnsTemp.push(element);
    });


    activeScheduleColumnsSearch = [...document.getElementsByClassName('active-column')];
    inactiveScheduleColumnsSearch = [...document.getElementsByClassName('inactive-column')];

}

const inactiveColumn = () => {
    activeScheduleColumnsSearch.forEach((element, index) => {

        if(element.checked){
            element.checked = false;
            inactiveScheduleColumnsSearch.push(element);
            activeScheduleColumnsSearch.splice(index, 1);

            const divId = element.id.replace('order', 'div');

            const elementToMove = document.getElementById(divId);

            document.getElementById('active-columns').removeChild(elementToMove);
            document.getElementById('inactive-columns').appendChild(elementToMove);
        }
    });
}

const activeColumn = () => {
    inactiveScheduleColumnsSearch.forEach((element, index) => {
        if(element.checked){
            element.checked = false;
            activeScheduleColumnsSearch.push(element);
            inactiveScheduleColumnsSearch.splice(index, 1);

            const divId = element.id.replace('order', 'div');

            const elementToMove = document.getElementById(divId);

            document.getElementById('inactive-columns').removeChild(elementToMove);
            document.getElementById('active-columns').appendChild(elementToMove);
        }
    });
}

const handleSelect = (radioElement) => {
    if(!radioElement.checked) return;

    const allColumns = activeScheduleColumnsSearch.concat(inactiveScheduleColumnsSearch);

    allColumns.forEach(element => {
        if(element.id !== radioElement.id){
            element.checked = false;
        }
    });
}

const restoreColumns = () => {

    let box = document.querySelector('#active-columns');
    let child = box.lastElementChild

    while (child) {
        box.removeChild(child);
        child = box.lastElementChild;
    }

    box = document.querySelector('#inactive-columns');
    child = box.lastElementChild

    while (child) {
        box.removeChild(child);
        child = box.lastElementChild;
    }

    box = document.querySelector('#active-columns');

    activeColumnsTemp.forEach(element => {
        box.append(element);  
    });

    box = document.querySelector('#inactive-columns');

    inactiveColumnsTemp.forEach(element => {
        box.append(element);  
    });

}

const moveColumn = (direction) => {

    const columns = document.querySelector('#active-columns').children;

    for(let x = 0; x < columns.length; x++ ){

        const element = columns[x];

        const divElement = element.children[0];
        const columnElement = divElement.children[0];

        if(!columnElement.checked) continue;

        if(columnElement.checked){

            if(x === columns.length - 1 && direction === 'down') break;

            if(x === 0 && direction === 'up') break;

            const container = document.querySelector('div[name="active-columns"]');
            const neighborElement = (direction === 'up') ? columns[x - 1 ] : columns[x + 1 ];

            if(direction === 'up') container.insertBefore(element, neighborElement);
            else container.insertBefore(neighborElement, element);
            break;
        }
    }
}

const saveOrder = () => {

    activeScheduleColumnsSearch.forEach(element => {
        element.checked = true;
    });

    document.getElementById('order-form').submit();
}

const handleChangeFiles = () => {

    const attachment = document.querySelector('#attachment');

    Array.from(attachment.files).forEach(file => {
        const parentSpan = document.createElement("span");
        parentSpan.classList.add("file-block");

        const childSpan = document.createElement("span");
        childSpan.classList.add("name");
        childSpan.innerHTML = file.name;

        const fileDelete = document.createElement("span");
        fileDelete.classList.add("file-delete");
        fileDelete.innerHTML = '+';
        fileDelete.setAttribute("onclick","removeFile(this, false)");

        parentSpan.appendChild(fileDelete);
        parentSpan.appendChild(childSpan);
        document.querySelector('#files-names').appendChild(parentSpan);
    });


    for (let file of attachment.files) {
		dt.items.add(file);
	}

    attachment.files = dt.files;
}

const handleReportChangeFiles = () => {

    const attachment = document.querySelector('#attachment');

    Array.from(attachment.files).forEach(file => {

        const size = file.size / 1000000;

        if(size > 5){
            alert('São permitidos apenas anexos com até 5MB!');
        }else{
            const parentSpan = document.createElement("span");
            parentSpan.classList.add("file-block");
    
            const childSpan = document.createElement("span");
            childSpan.classList.add("name");
            childSpan.innerHTML = file.name;
    
            const fileDelete = document.createElement("span");
            fileDelete.classList.add("file-delete");
            fileDelete.innerHTML = '+';
            fileDelete.setAttribute("onclick","removeFile(this, false)");
    
            parentSpan.appendChild(fileDelete);
            parentSpan.appendChild(childSpan);
    
            const filesNames = document.querySelector('#files-names');
    
            while (filesNames.firstChild) {
                filesNames.removeChild(filesNames.firstChild);
            }
    
            filesNames.appendChild(parentSpan);
        }
    });
}

const removeFile = (element, removeToEdit) => {

    console.log(element);
    if(removeToEdit) addToRemove(element.id);
    const sibling = element.nextSibling;
    const parent = element.parentElement;

    let name = sibling.innerHTML;


    while (parent.firstChild) {
        parent.firstChild.remove()
    }

    for(let i = 0; i < dt.items.length; i++){

        if(name === dt.items[i].getAsFile().name){
            dt.items.remove(i);
            continue;
        }
    }

    document.getElementById('attachment').files = dt.files;

}

const addToRemove = (attachmentId) => {

    const elements = document.getElementById('filesToRemove');

    let ids = elements.value;

    if(elements.value) ids += `,${attachmentId}`;
    else ids = attachmentId;

    elements.value = ids;
}

const customAlert = (type, message) => {

    let alert = document.getElementById('fixed-alert');

    if(alert) document.body.removeChild(alert);

    const div = document.createElement("div");
    div.classList.add('alert');
    div.classList.add(type);
    div.classList.add('alert-dismissible');
    div.classList.add('show');
    div.setAttribute('role', 'alert');
    div.setAttribute('id', 'fixed-alert');

    div.innerHTML = message;

    const btn = document.createElement("button");
    btn.classList.add('close');
    btn.setAttribute('data-dismiss', 'alert');
    btn.setAttribute('aria-label', 'Close');
    btn.setAttribute('type', 'button');

    const span = document.createElement("span");
    span.setAttribute('aria-hidden', true);
    span.innerHTML = '&times;';

    btn.appendChild(span);
    div.appendChild(btn);

    document.body.appendChild(div);

    setTimeout(() => {

        alert = document.getElementById('fixed-alert');
        if(alert) document.body.removeChild(alert);

    }, 5000);
}

const HandleChangeAutomatedTimeSwitch = () => {
    const automatedTimeSwitch = document.getElementById('automatedTimeSwitch');

    if(!automatedTimeSwitch) return;

    if(!automatedTimeSwitch.checked) automatedTimeIsOn = false;
    else {
        automatedTimeIsOn = true;
        progressTimer();
    }
}

const progressTimer = () => {

    let time = PROGRESS_TIME;
    idInterval = setInterval(() => {

        if(!automatedTimeIsOn) clearInterval(idInterval);

        time = time - 10;

        if(document.getElementById('panel-progress')){
            document.getElementById('panel-progress').value = time;
            if(time === 0) window.location.reload();;
        }

    }, 10);
}

const navigateToSearch = (scheduleStatus) => {

    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    window.location =  `index.php?conteudo=searchSchedule.php&status=${scheduleStatus}&startDate=${startDate}&endDate=${endDate}`;
}

const backHistory = () => {
    history.back()
}

const checkPassword = (password2) => {
    
    const feedbackLabel = document.getElementById('passwordFeedback');
    const saveBtn = document.getElementById('user-save-btn');
    const passwordValue = document.getElementById('password').value;


    if(passwordValue == password2.value){
        feedbackLabel.innerHTML = 'Senhas iguais';
        saveBtn.disabled = false;
        return;
    }
    

    feedbackLabel.innerHTML = 'Senhas não conferem';
    saveBtn.disabled = true;
}

const checkUserType = (element) => {

    const clientSelectField = document.getElementById('business');

    if(element.value == 'client') {
        clientSelectField.disabled = false;
        clientSelectField.required = true;
        return;
    }

    clientSelectField.disabled = true;
    clientSelectField.required = false;
    clientSelectField.selectedIndex = 0;
}

const manageVehiclePlate = (element) => {

    const vehiclePlateField = document.getElementById('vehiclePlate');

    if(element.value.length > 0) {

        vehiclePlateField.disabled = false;
        vehiclePlateField.required = true;
        return;
    }

    vehiclePlateField.disabled = true;
    vehiclePlateField.required = false;
}

const manageVehicleTypes = (element) => {

    const vehiclePlateField = document.getElementById('vehiclePlate');
    const vehiclePlateField2 = document.getElementById('vehiclePlate2');
    const vehiclePlateField3 = document.getElementById('vehiclePlate3');

    if(element.value) {

        vehiclePlateField.disabled = false;
        vehiclePlateField.required = true;

        vehiclePlateField2.disabled = false;
        vehiclePlateField3.disabled = false;
        return;
    }

    vehiclePlateField.disabled = true;
    vehiclePlateField.required = false;

    vehiclePlateField2.disabled = true;
    vehiclePlateField3.disabled = true; 
}

const manegeFieldViewByValue = (element, idFieldView, isRequired, value) => {

    const field = document.getElementById(idFieldView);

    if(value){
        if(element.value == value) {
    
            field.disabled = false;
            field.required = isRequired;
            return;
        }
    } else{
        if(element.value) {
    
            field.disabled = false;
            field.required = isRequired;
            return;
        }
    }

    field.disabled = true;
    field.required = false;

    field.value = '';
}

const manageCnhValidation = (element, valueToValidation) => {

    const requiredCnh = document.getElementById('requiredCnh');
    const requiredCnhExpiration = document.getElementById('requiredCnhExpiration');

    if(element.value != valueToValidation){
        requiredCnh.hidden = true;
        requiredCnh.required = false;

        requiredCnhExpiration.hidden = true;
        requiredCnhExpiration.required = false;
        return;
    }

    requiredCnh.required = true;
    requiredCnh.hidden = false;

    requiredCnhExpiration.required = true;
    requiredCnhExpiration.hidden = false;
}

const setTableLength = (qtde) => {

    const tables = document.getElementsByName('dataTables-example_length');
    const tables2 = document.getElementsByName('dataTables-example2_length');
    
    if(!tables) return;

    if(document.getElementById('search-table')) qtde = 10;

    setTimeout(() => {
        tables.forEach(element => {
            element.value = qtde;
            element.dispatchEvent(new Event("change"));
        });

        tables2.forEach(element => {
            element.value = qtde;
            element.dispatchEvent(new Event("change"));
        });
    }, 200);

}

const checkDriverAccessSubmit = () => {
    return document.getElementById('driverId').value; 
}

const checkEmployeeAccessSubmit = () => {
    return document.getElementById('employeeId').value; 
}

const manageEndDate = (element) => {

    const btn = document.getElementById('user-save-btn');

    if(element.value.length == 0){
        btn.innerHTML = 'Criar acesso';
        return;
    }

    btn.innerHTML = 'Encerrar acesso';
}

const manageRedirect = (formId) =>{
    document.getElementById('redirect').value = 'redirect';
    const form = document.getElementById(formId);

    if(!form.checkValidity()){
        form.reportValidity();
        return;
    }

    if(checkImageProfile()){
        document.getElementById(formId).submit();
    }
}

const getCameras = async () => {

    cameraList = [];
    selectedCamera = {video: true, audio:false};
    cameraToSwap = {video: true, audio:false};

    hasCameraChanged = false;
    currentStream = null;

    if (navigator.mediaDevices.getUserMedia) {

        await navigator.mediaDevices.enumerateDevices({}).then(gotDevices => {
            gotDevices.forEach( (camera) => {
                if(camera.kind === 'videoinput'){

                    const cameraDetail = {};
                    cameraDetail.deviceId = camera.deviceId;
                    cameraDetail.label = (camera.label.includes('Integrated')) ? 'Câmera integrada' : 'camera ' + (cameraList.length + 1);
                    cameraList.push(cameraDetail);
                }
            });
        });
    }
}

const startCamera = async () => {
    await getCameras();

    const firstConstraint = { deviceId: null };
    const secondConstraint = { deviceId: null };

    if(cameraList.length < 1) {
        console.log('Não foram encontradas câmeras conectadas');
        closeCamera();
        return;
    }

    if(cameraList.length > 1){

        document.querySelector('#swap-camera').style.display = 'block';

        cameraList.forEach(element => {

            if(element.label === 'Câmera integrada'){
                firstConstraint.deviceId = element.deviceId;
                cameraToSwap.video = firstConstraint;
            } 
            else {
                secondConstraint.deviceId = element.deviceId;
                selectedCamera.video = secondConstraint;
                openCamera(selectedCamera);
            }
        });

    }else{
        document.querySelector('#swap-camera').style.display = 'none';
        firstConstraint.deviceId = cameraList[0].deviceId;
        selectedCamera.video = firstConstraint;
        openCamera(selectedCamera);
    } 
}

const openCamera = (cameraToShow) => {
    const video = document.querySelector("#videoElement");
    navigator.mediaDevices.getUserMedia(cameraToShow)
    .then( (stream) => {
        currentStream = stream;
        video.srcObject = stream;
    })
    .catch((error) => {
        console.log("Erro ao abrir a câmera", error);
    });
}

const swapCamera = () => {

    if(!hasCameraChanged) {
        hasCameraChanged = true;
        closeCamera();
        openCamera(cameraToSwap);
    }else{
        hasCameraChanged = false;
        closeCamera();
        openCamera(selectedCamera);
    }
}

const closeCamera = () => {

    const video = document.querySelector("#videoElement").srcObject;

    if(video){
        const tracks = video.getTracks();
    
        tracks[0].stop();
        tracks.forEach(track => track.stop())
    }
}

const closeModal = () => {
    $(".close").click();
}

const takepicture = () => {
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext("2d");
    const video = document.querySelector("#videoElement");
    const photo = document.getElementById('profile-image');

    const width = 270;
    const height = 200;

    canvas.width = width;
    canvas.height = height;
    context.drawImage(video, 0, 0, width, height);

    const data = canvas.toDataURL("image/png");
    photo.setAttribute("src", data);
    document.getElementsByName('image-profile')[0].value = data;
    document.getElementById('image-profile-check').value = data;

    document.getElementById('image-profile-feedback').style.display = 'none';

    closeCamera();
    closeModal();
}

const manageListAccess = () => {

    setTimeout(() => {
        const toogle = document.getElementById('access-type-toogle');

        const headerTable = document.getElementById('dataTables-example_wrapper');
        const vehicleAccess = document.getElementsByClassName('vehicle-access');
        const employeeAccess = document.getElementsByClassName('employee-access');

        const autoComplete = document.getElementById('auto-complete');
    
        const label = document.getElementById('access-type-label');
        autoComplete.value = null;
    
        for(let x = 0; x < 3; x++){
    
            if(!toogle.checked){
                isDriversList = false;
                headerTable.hidden = true;
                vehicleAccess[x].hidden = true;
                employeeAccess[x].hidden = false; 
                label.innerHTML = 'COLABORADORES';
                document.getElementById('employeeExport').hidden = false;
                document.getElementById('driverExport').hidden = true;
                autoComplete.placeholder = 'Colaborador';

                autocomplete(document.getElementById("auto-complete"), employessRecordList);
            }else{
                isDriversList = true;
                headerTable.hidden = false;
                vehicleAccess[x].hidden = false;
                employeeAccess[x].hidden = true; 
                label.innerHTML = 'VEÍCULOS';
                document.getElementById('employeeExport').hidden = true;
                document.getElementById('driverExport').hidden = false;
                autoComplete.placeholder = 'Motorista';

                autocomplete(document.getElementById("auto-complete"), driversRecordList);
            }
        }
    }, 20);
}

const checkImageProfile = () => {

    const element = document.getElementById('image-profile-check');
    if(element.value && element.value != '../images/profile.jpg') return true;

    document.getElementById('image-profile-feedback').style.display = 'block';

    return false;
}

const checkFieldHasValue = (element, buttonId) => {

    const button = document.getElementById(buttonId);

    if(element.value.length > 0){
        button.disabled = false;
        return;
    }

    button.disabled = true;
}

const ajaxNewShippingCompany = () => {

    const shippingCompanyName = document.getElementById('new-shipping-company-name');

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        console.log(this);
        if (this.readyState == 4 && this.status == 200) {
            
            const name = this.responseText;
            const feedback = document.getElementById('feedback-modal');

            if(name.includes('ERROR')){
                feedback.style.display = 'block';
                feedback.innerHTML = name.split('-')[1];
                return;
            }else{
                feedback.style.display = 'none';

                const newOption = document.createElement('option');
                const optionText = document.createTextNode(name);
                newOption.appendChild(optionText);
                newOption.setAttribute('value', name);
                newOption.setAttribute('selected', true);
    
                const select = document.getElementById('shippingCompany'); 
                select.appendChild(newOption);

                shippingCompanyName.value = '';
    
                $(".close").click();
                return;
            }
        }
    };
    xmlhttp.open("GET","../ajax/ajaxNewCompany.php?name="+shippingCompanyName.value, true);
    xmlhttp.send();
}

const ajaxNewBusinessClient = () => {

    const name = document.getElementById('new-business-client-name').value;
    const clientId = document.getElementById('business').value;

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const businessClientSelect = document.getElementById('business');
            getBusinessClient(businessClientSelect);
            $(".close").click();
        }
    };
    xmlhttp.open("GET","../ajax/ajaxNewBusinessClient.php?name="+name+"&clientId="+clientId, true);
    xmlhttp.send();
}

const autocomplete = (inp, arr) =>  {
   
    var currentFocus;

    inp.addEventListener("input", function(e) {
        var a, b, i, val = this.value;

        closeAllLists();
        if (!val) { return false;}
        currentFocus = -1;
        
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        
        this.parentNode.appendChild(a);
        
        for (i = 0; i < arr.length; i++) {
         
            if (arr[i].toUpperCase().includes(val.toUpperCase())) {
            
                b = document.createElement("DIV");
                
                b.innerHTML =  arr[i].substr(0, val.length) ;
                b.innerHTML += arr[i].substr(val.length);
                
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                
                b.addEventListener("click", function(e) {
                    
                    inp.value = this.getElementsByTagName("input")[0].value;
                    closeAllLists();
                });

                a.appendChild(b);
            }
        }
    });
    
    inp.addEventListener("keydown", function(e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
          
            currentFocus++;
         
            addActive(x);
        } else if (e.keyCode == 38) {
          
            currentFocus--;
         
            addActive(x);
        } else if (e.keyCode == 13) {
         
            e.preventDefault();
            if (currentFocus > -1) {
                
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
      
        if (!x) return false;
        
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
      
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
      
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
            x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
};

const getDrivers = async () => {

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
       
        if (this.readyState == 4 && this.status == 200) {

            const driversResult = this.responseText.split('|');
            
            driversResult.forEach(element => {

                const result = element.split(';');

                const driver = `${result[2]} - ${result[1]}`;
                driversRecordList.push(driver);

                driversToSearch[driver] = Number(result[0]);
            });
        }
    };
    xmlhttp.open("GET","../ajax/ajaxGetDrivers.php", true);
    xmlhttp.send();
}

const getBusinessClient = async (element, isList) => {

    const clientId = element.value;
    const select = document.getElementById('businessClient');

    while (select.options.length > 0) {                
        select.remove(0);
    } 

    let opt = document.createElement('option');

    if(!isList){
        opt.value = "";
        opt.innerHTML = 'Selecione...';
    }else{
        opt.value = "all";
        opt.innerHTML = 'Todas';
    }
    select.appendChild(opt);

    const newBusinessClientAction = document.getElementById('new-business-client-action');

    if(!clientId) {
        select.disabled = true;
        if(!isList) newBusinessClientAction.disabled = true;
        return;
    }
    else {
        select.disabled = false;
        if(!isList) newBusinessClientAction.disabled = false;
    }

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        
        if (this.readyState == 4 && this.status == 200) {
            
            const result = this.responseText.split('|');

            if(!result[0]) return;
            result.forEach(element => {

                const values = element.split('-');
                opt = document.createElement('option');
                opt.value = values[0];
                opt.innerHTML = values[1];
                select.appendChild(opt);
            });
        }
    };
    xmlhttp.open("GET","../ajax/ajaxGetBusinessClient.php?clientId="+clientId, true);
    xmlhttp.send();
}

const getEmployess = async () => {

    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
       
        if (this.readyState == 4 && this.status == 200) {

            const employessResult = this.responseText.split('|');
            
            employessResult.forEach(element => {

                const result = element.split(';');

                const employee = `${result[2]} - ${result[1]}`;
                employessRecordList.push(employee);

                employessToSearch[employee] = Number(result[0]);
            });
        }
    };
    xmlhttp.open("GET","../ajax/ajaxGetEmployess.php", true);
    xmlhttp.send();
}

const navigateToAccessNew = () => {

    const imputValue = document.getElementById('auto-complete').value;

    let id;

    if(isDriversList) id = driversToSearch[imputValue];
    else id = employessToSearch[imputValue];

    if(!id) return;

    if(!isNaN(id)){

        if(isDriversList) window.location.href = 'index.php?content=newDriverAccess.php&driverId='+id;
        else window.location.href = 'index.php?content=newEmployeeAccess.php&employeeId='+id;
    }
}
  
