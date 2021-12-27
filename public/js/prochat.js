let _ProChatVariables_ = {
    storageClient: '_prochat.client',
    storageSession: '_prochat.session',
    storageOperator: '_prochat.operator',
    urlApi: 'https://api.prosystemsc.com/prochat',
    urlSocket: 'wss://socket.prosystemsc.com'
};

class ProChatDocument {

    setElement(name) {
        this.type = name[0];
        this.name = name.substr(1);
    }

    show() {
        if (this.type == '#') {
            document.getElementById(this.name).style.display = 'block';
        }
        else if (this.type == '.') {
            let elements = document.getElementsByClassName(this.name);
            for (let i = 0; i < elements.length; i++) {
                elements[i].style.display = 'block';
            }
        }
    }

    hide() {
        if (this.type == '#') {
            document.getElementById(this.name).style.display = 'none';
        }
        else if (this.type == '.') {
            let elements = document.getElementsByClassName(this.name);
            for (let i = 0; i < elements.length; i++) {
                elements[i].style.display = 'none';
            }
        }
    }

    toogleClass(className) {
        if (this.type == '#') {
            let element = document.getElementById(this.name);
            if (this.hasClass(element, className)) {
                element.classList.remove(className);
            }
            else {
                element.classList.add(className);
            }
        }
        else if (this.type == '.') {
            let elements = document.getElementsByClassName(this.name);
            for (let i = 0; i < elements.length; i++) {
                if (this.hasClass(elements[i], className)) {
                    elements[i].classList.remove(className);
                }
                else {
                    elements[i].classList.add(className);
                }
            }
        }
    }

    hasClass(element, className) {
        return new RegExp(' ' + className + ' ').test(' ' + element.className + ' ');
    }

}

class _ProChat {

    start(config) {

        this.client = config;

        if (this.client == undefined) {
            throw new Error('token not found');
        }

        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let res = JSON.parse(this.responseText);

                if (res.success) {
                    localStorage.setItem(_ProChatVariables_.storageClient, JSON.stringify(res.data));

                    /******* Load Component *********/

                    let link = document.createElement('link');
                    link.type = 'text/css';
                    link.rel = 'stylesheet';
                    link.href = 'https://api.prosystemsc.com/css/prochat.css';

                    let head = document.head;
                    head.appendChild(link);

                    let elementBody = document.querySelector('body');
                    elementBody.insertAdjacentHTML('beforeend', `
                        <div class="_ProChat_fabs">
                            <div class="_ProChat_chat">
                                <div id="_ProChat_header" class="_ProChat_chat_header">
                                    <div class="_ProChat_chat_option">
                                        <div class="header_img">
                                            <img id="_ProChat_photo_client" />
                                        </div>
                                        <span id="_ProChat_name_client"></span>
                                        <i class="zmdi zmdi-circle online"></i><br>
                                        <span id="_ProChat_title_client"></span>
                                    </div>
                                </div>
                                <div id="_ProChat_chat_form" class="_ProChat_chat_converse _ProChat_chat_form">
                                </div>
                                <div class="_ProChat_fab_field" id="_ProChat_send_msg" style="display:none">
                                    <a id="_ProChat_fab_camera" class="_ProChat_fab"><i class="zmdi zmdi-camera"></i></a>
                                    <a id="_ProChat_fab_send" class="_ProChat_fab"><i class="zmdi zmdi-mail-send"></i></a>
                                    <textarea id="_ProChat_message" name="chat_message" placeholder="Envie uma mensagem"
                                        class="_ProChat_chat_field chat_message"></textarea>
                                </div>
                            </div>
                            <a id="_ProChat_prime" class="_ProChat_fab"><i class="_ProChat_prime zmdi zmdi-comment-outline"></i></a>
                        </div>
                    `);

                    _ProChat_Document.setElement('._ProChat_chat');
                    _ProChat_Document.hide();

                    document.getElementById('_ProChat_prime').onclick = () => {

                        _ProChat_Document.setElement('._ProChat_chat');
                        _ProChat_Document.show();
                        _ProChat_Document.toogleClass('_ProChat_is-visible');

                        _ProChat_Document.setElement('._ProChat_prime');
                        _ProChat_Document.toogleClass('zmdi-comment-outline');
                        _ProChat_Document.toogleClass('zmdi-close');
                        _ProChat_Document.toogleClass('_ProChat_is-active');
                        _ProChat_Document.toogleClass('_ProChat_is-visible');

                        _ProChat_Document.setElement('#_ProChat_prime');
                        _ProChat_Document.toogleClass('_ProChat_is-float');

                        _ProChat_Document.setElement('._ProChat_fab');
                        _ProChat_Document.toogleClass('_ProChat_is-visible');

                        document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);

                    };

                    let client = JSON.parse(localStorage.getItem(_ProChatVariables_.storageClient));

                    document.getElementById('_ProChat_photo_client').src = client.photo;

                    document.getElementById('_ProChat_header').style.background = client.widget_color1;
                    document.getElementById('_ProChat_header').style.background = `-webkit-linear-gradient(to right, ${client.widget_color1}, ${client.widget_color2})`;;
                    document.getElementById('_ProChat_header').style.background = `linear-gradient(to right, ${client.widget_color1}, ${client.widget_color2})`;

                    document.getElementById('_ProChat_prime').style.background = client.widget_color1;

                    document.getElementById('_ProChat_name_client').innerText = client.name;

                    document.getElementById('_ProChat_title_client').innerText = client.title;


                    /*******************************/

                    if (localStorage.getItem(_ProChatVariables_.storageSession) == null) {

                        let element = document.querySelector('#_ProChat_chat_form');

                        const client = JSON.parse(localStorage.getItem(_ProChatVariables_.storageClient));

                        element.insertAdjacentHTML('beforeend', `
                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin _ProChat_chat_msg_item_login" id="_ProChat_form_msg">
                                <div class="_ProChat_chat_avatar">
                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                </div>
                                <span>Por favor, apresente-se no chat!</span>
                                <div>
                                    <form class="_ProChat_message_form">
                                        <input id="_ProChat_form_name" class="_ProChat_chat_input" placeholder="Seu nome" />
                                        <small id="_ProChat_form__ProChat_err" style="font-weight:none;color:#DC3545"></small>
                                        <input id="_ProChat_form_phone" class="_ProChat_chat_input" placeholder="Seu telefone" />
                                        <small id="_ProChat_form_phone_err" style="font-weight:none;color:#DC3545"></small>
                                        <input id="_ProChat_form_email" class="_ProChat_chat_input" placeholder="Seu email" />
                                        <small id="_ProChat_form_email_err" style="font-weight:none;color:#DC3545"></small>
                                        <button type="button" id="_ProChat_sendFormSession">Enviar</button>
                                    </form>
                                </div>
                            </span>
                        `);

                        document.getElementById('_ProChat_sendFormSession').onclick = () => {

                            let session = {};

                            session.name = document.getElementById('_ProChat_form_name').value;
                            session.phone = document.getElementById('_ProChat_form_phone').value;
                            session.email = document.getElementById('_ProChat_form_email').value;

                            let err = false;

                            document.getElementById('_ProChat_form__ProChat_err').innerText = '';
                            document.getElementById('_ProChat_form_phone_err').innerText = '';
                            document.getElementById('_ProChat_form_email_err').innerText = '';

                            document.getElementById('_ProChat_form_name').style.border = '1px solid #e0e0e0';
                            document.getElementById('_ProChat_form_phone').style.border = '1px solid #e0e0e0';
                            document.getElementById('_ProChat_form_email').style.border = '1px solid #e0e0e0';

                            if (session.name.length < 1) {
                                document.getElementById('_ProChat_form__ProChat_err').innerText = 'Informe seu nome';
                                document.getElementById('_ProChat_form_name').style.border = '1px solid #DC3545';
                                err = true;
                            }

                            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(session.email)) {
                                document.getElementById('_ProChat_form_email_err').innerText = 'Informe um email válido';
                                document.getElementById('_ProChat_form_email').style.border = '1px solid #DC3545';
                                err = true;
                            }

                            if ((session.phone.length > 0 && session.phone.match(/\d/g).length < 5) || session.phone.length == 0) {
                                document.getElementById('_ProChat_form_phone_err').innerText = 'Informe um telefone válido';
                                document.getElementById('_ProChat_form_phone').style.border = '1px solid #DC3545';
                                err = true;
                            }

                            if (err) return;

                            let xhttp = new XMLHttpRequest();
                            xhttp.onreadystatechange = function () {
                                if (this.readyState == 4 && this.status == 200) {
                                    let res = JSON.parse(this.responseText);

                                    if (!res.success) throw 'Sessão não iniciada, atualize a página e tente novamente!';

                                    document.getElementById('_ProChat_sendFormSession').style.background = '#ccc';
                                    document.getElementById('_ProChat_sendFormSession').innerHTML = '&#127881; Obrigado';

                                    session.id = res.data.id;

                                    localStorage.setItem(_ProChatVariables_.storageSession, JSON.stringify(session));

                                    let client = JSON.parse(localStorage.getItem(_ProChatVariables_.storageClient));

                                    let element = document.querySelector('#_ProChat_chat_form');

                                    const operator = JSON.parse(localStorage.getItem(_ProChatVariables_.storageOperator));

                                    this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                    this.socket.onopen = function () {

                                        let xhttp = new XMLHttpRequest();
                                        xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                        xhttp.setRequestHeader('Content-type', 'application/json');
                                        xhttp.send(JSON.stringify({ session: session.id }));
                                    }

                                    this.socket.onmessage = (event) => {

                                        const res = JSON.parse(event.data);

                                        if (res.type == 'text_message') {

                                            element.insertAdjacentHTML('beforeend', `
                                                <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                    <div class="_ProChat_chat_avatar">
                                                        <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                    </div>
                                                    <span>${res.message}</span>
                                                </span>
                                            `);

                                        }
                                        else if (res.type == 'typing_start') {

                                            element.insertAdjacentHTML('beforeend', `
                                                <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                    <div class="_ProChat_chat_avatar">
                                                        <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                    </div>
                                                    <span class="_ProChat_typing">digitando...</span>
                                                </span>
                                            `);

                                        }
                                        else if (res.type == 'typing_finish') {
                                            document.getElementById('_ProChat_msg_tmp').remove();
                                        }
                                        else if (res.type == 'add_operator') {
                                            localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                            document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                            document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                        }
                                        else if (res.type == 'remove_operator') {
                                            localStorage.removeItem(_ProChatVariables_.storageOperator);
                                        }

                                        document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                    };

                                    setInterval(() => {
                                        if (this.socket.readyState > 1) {
                                            this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                            this.socket.onopen = function () {

                                                let xhttp = new XMLHttpRequest();
                                                xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                                xhttp.setRequestHeader('Content-type', 'application/json');
                                                xhttp.send(JSON.stringify({ session: session.id }));
                                            }

                                            this.socket.onmessage = (event) => {

                                                const res = JSON.parse(event.data);

                                                if (res.type == 'text_message') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span>${res.message}</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_start') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span class="_ProChat_typing">digitando...</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_finish') {
                                                    document.getElementById('_ProChat_msg_tmp').remove();
                                                }
                                                else if (res.type == 'add_operator') {
                                                    localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                                    document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                                    document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                                }
                                                else if (res.type == 'remove_operator') {
                                                    localStorage.removeItem(_ProChatVariables_.storageOperator);
                                                }

                                                document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                            };
                                        }
                                    }, 30 * 1000);

                                    document.getElementById('_ProChat_fab_send').onclick = () => {

                                        const message = document.getElementById('_ProChat_message').value;

                                        if (message != "") {

                                            document.getElementById('_ProChat_message').value = "";

                                            element.insertAdjacentHTML('beforeend', `
                                                <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_user" style="background:${client.widget_color1}">
                                                    <span>${message}</span>
                                                </span>
                                            `);

                                            document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);

                                            if (this.socket.readyState > 1) {

                                                this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                                this.socket.onopen = function () {
                                                    let xhttp = new XMLHttpRequest();
                                                    xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                                    xhttp.setRequestHeader('Content-type', 'application/json');
                                                    xhttp.send(JSON.stringify({ session: session.id }));
                                                }

                                                this.socket.onmessage = (event) => {

                                                    const res = JSON.parse(event.data);

                                                    if (res.type == 'text_message') {

                                                        element.insertAdjacentHTML('beforeend', `
                                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                                <div class="_ProChat_chat_avatar">
                                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                                </div>
                                                                <span>${res.message}</span>
                                                            </span>
                                                        `);

                                                    }
                                                    else if (res.type == 'typing_start') {

                                                        element.insertAdjacentHTML('beforeend', `
                                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                                <div class="_ProChat_chat_avatar">
                                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                                </div>
                                                                <span class="_ProChat_typing">digitando...</span>
                                                            </span>
                                                        `);

                                                    }
                                                    else if (res.type == 'typing_finish') {
                                                        document.getElementById('_ProChat_msg_tmp').remove();
                                                    }
                                                    else if (res.type == 'add_operator') {
                                                        localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                                        document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                                        document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                                    }
                                                    else if (res.type == 'remove_operator') {
                                                        localStorage.removeItem(_ProChatVariables_.storageOperator);
                                                    }

                                                    document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                                };

                                                this.socket.send(JSON.stringify({
                                                    type: 'text_message',
                                                    client: client.id,
                                                    session: session.id,
                                                    operator: operator != null ? operator.id : null,
                                                    message: message
                                                }));

                                            }
                                            else {

                                                this.socket.send(JSON.stringify({
                                                    type: 'text_message',
                                                    client: client.id,
                                                    session: session.id,
                                                    operator: operator != null ? operator.id : null,
                                                    message: message
                                                }));

                                            }

                                            let xhttp = new XMLHttpRequest();
                                            xhttp.open('POST', `${_ProChatVariables_.urlApi}/sessionSendMessage`, true);
                                            xhttp.setRequestHeader('Content-type', 'application/json');
                                            xhttp.send(JSON.stringify({
                                                client: client.id,
                                                session: session.id,
                                                message: message
                                            }));
                                        }
                                    }

                                    document.getElementById('_ProChat_message').onfocus = () => {

                                        if (this.socket.readyState > 1) {

                                            this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                            this.socket.onopen = function () {
                                                let xhttp = new XMLHttpRequest();
                                                xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                                xhttp.setRequestHeader('Content-type', 'application/json');
                                                xhttp.send(JSON.stringify({ session: session.id }));
                                            }

                                            this.socket.onmessage = (event) => {

                                                const res = JSON.parse(event.data);

                                                if (res.type == 'text_message') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span>${res.message}</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_start') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span class="_ProChat_typing">digitando...</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_finish') {
                                                    document.getElementById('_ProChat_msg_tmp').remove();
                                                }
                                                else if (res.type == 'add_operator') {
                                                    localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                                    document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                                    document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                                }
                                                else if (res.type == 'remove_operator') {
                                                    localStorage.removeItem(_ProChatVariables_.storageOperator);
                                                }

                                                document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                            };

                                            this.socket.send(JSON.stringify({
                                                type: 'typing_start',
                                                client: client.id,
                                                session: session.id,
                                                operator: operator != null ? operator.id : null
                                            }));

                                        }
                                        else {

                                            this.socket.send(JSON.stringify({
                                                type: 'typing_start',
                                                client: client.id,
                                                session: session.id,
                                                operator: operator != null ? operator.id : null
                                            }));
                                        }
                                    };

                                    document.getElementById('_ProChat_message').onblur = () => {

                                        if (this.socket.readyState > 1) {

                                            this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                            this.socket.onopen = function () {
                                                let xhttp = new XMLHttpRequest();
                                                xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                                xhttp.setRequestHeader('Content-type', 'application/json');
                                                xhttp.send(JSON.stringify({ session: session.id }));
                                            }

                                            this.socket.onmessage = (event) => {

                                                const res = JSON.parse(event.data);

                                                if (res.type == 'text_message') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span>${res.message}</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_start') {

                                                    element.insertAdjacentHTML('beforeend', `
                                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                            <div class="_ProChat_chat_avatar">
                                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                            </div>
                                                            <span class="_ProChat_typing">digitando...</span>
                                                        </span>
                                                    `);

                                                }
                                                else if (res.type == 'typing_finish') {
                                                    document.getElementById('_ProChat_msg_tmp').remove();
                                                }
                                                else if (res.type == 'add_operator') {
                                                    localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                                    document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                                    document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                                }
                                                else if (res.type == 'remove_operator') {
                                                    localStorage.removeItem(_ProChatVariables_.storageOperator);
                                                }

                                                document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                            };

                                            this.socket.send(JSON.stringify({
                                                type: 'typing_finish',
                                                client: client.id,
                                                session: session.id,
                                                operator: operator != null ? operator.id : null
                                            }));

                                        }
                                        else {

                                            this.socket.send(JSON.stringify({
                                                type: 'typing_finish',
                                                client: client.id,
                                                session: session.id,
                                                operator: operator != null ? operator.id : null
                                            }));
                                        }
                                    };

                                    element.insertAdjacentHTML('beforeend', `
                                        <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                            <div class="_ProChat_chat_avatar">
                                                <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                            </div>
                                            <span class="_ProChat_typing">digitando...</span>
                                        </span>
                                    `);

                                    document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);

                                    setTimeout(() => {

                                        document.getElementById('_ProChat_msg_tmp').remove();

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span>${client.initial_message}</span>
                                            </span>
                                        `);

                                        document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);

                                    }, 3000);

                                    document.getElementById('_ProChat_send_msg').style.display = 'block';
                                }
                            };
                            xhttp.open('POST', `${_ProChatVariables_.urlApi}/initSession`, true);
                            xhttp.setRequestHeader('Content-type', 'application/json');
                            xhttp.send(JSON.stringify(session));

                        }

                    }

                    else {

                        let element = document.querySelector('#_ProChat_chat_form');

                        const client = JSON.parse(localStorage.getItem(_ProChatVariables_.storageClient));

                        const session = JSON.parse(localStorage.getItem(_ProChatVariables_.storageSession));

                        const operator = JSON.parse(localStorage.getItem(_ProChatVariables_.storageOperator));

                        this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                        this.socket.onopen = function () {
                            let xhttp = new XMLHttpRequest();
                            xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                            xhttp.setRequestHeader('Content-type', 'application/json');
                            xhttp.send(JSON.stringify({ session: session.id }));
                        }

                        this.socket.onmessage = (event) => {

                            const res = JSON.parse(event.data);

                            if (res.type == 'text_message') {

                                element.insertAdjacentHTML('beforeend', `
                                    <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                        <div class="_ProChat_chat_avatar">
                                            <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                        </div>
                                        <span>${res.message}</span>
                                    </span>
                                `);

                            }
                            else if (res.type == 'typing_start') {

                                element.insertAdjacentHTML('beforeend', `
                                    <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                        <div class="_ProChat_chat_avatar">
                                            <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                        </div>
                                        <span class="_ProChat_typing">digitando...</span>
                                    </span>
                                `);

                            }
                            else if (res.type == 'typing_finish') {
                                document.getElementById('_ProChat_msg_tmp').remove();
                            }
                            else if (res.type == 'add_operator') {
                                localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                document.getElementById('_ProChat_title_client').innerText = res.data.title;
                            }
                            else if (res.type == 'remove_operator') {
                                localStorage.removeItem(_ProChatVariables_.storageOperator);
                            }

                            document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                        };

                        setInterval(() => {
                            if (this.socket.readyState > 1) {
                                this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                this.socket.onopen = function () {
                                    let xhttp = new XMLHttpRequest();
                                    xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                    xhttp.setRequestHeader('Content-type', 'application/json');
                                    xhttp.send(JSON.stringify({ session: session.id }));
                                }

                                this.socket.onmessage = (event) => {

                                    const res = JSON.parse(event.data);

                                    if (res.type == 'text_message') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span>${res.message}</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_start') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span class="_ProChat_typing">digitando...</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_finish') {
                                        document.getElementById('_ProChat_msg_tmp').remove();
                                    }
                                    else if (res.type == 'add_operator') {
                                        localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                        document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                        document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                    }
                                    else if (res.type == 'remove_operator') {
                                        localStorage.removeItem(_ProChatVariables_.storageOperator);
                                    }

                                    document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                };
                            }
                        }, 30 * 1000);

                        document.getElementById('_ProChat_fab_send').onclick = () => {

                            const message = document.getElementById('_ProChat_message').value;

                            if (message != "") {

                                document.getElementById('_ProChat_message').value = "";

                                element.insertAdjacentHTML('beforeend', `
                                    <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_user" style="background:${client.widget_color1}">
                                        <span>${message}</span>
                                    </span>
                                `);

                                document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);

                                if (this.socket.readyState > 1) {

                                    this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                    this.socket.onopen = function () {
                                        let xhttp = new XMLHttpRequest();
                                        xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                        xhttp.setRequestHeader('Content-type', 'application/json');
                                        xhttp.send(JSON.stringify({ session: session.id }));
                                    }

                                    this.socket.onmessage = (event) => {

                                        const res = JSON.parse(event.data);

                                        if (res.type == 'text_message') {

                                            element.insertAdjacentHTML('beforeend', `
                                                <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                    <div class="_ProChat_chat_avatar">
                                                        <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                    </div>
                                                    <span>${res.message}</span>
                                                </span>
                                            `);

                                        }
                                        else if (res.type == 'typing_start') {

                                            element.insertAdjacentHTML('beforeend', `
                                                <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                    <div class="_ProChat_chat_avatar">
                                                        <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                    </div>
                                                    <span class="_ProChat_typing">digitando...</span>
                                                </span>
                                            `);

                                        }
                                        else if (res.type == 'typing_finish') {
                                            document.getElementById('_ProChat_msg_tmp').remove();
                                        }
                                        else if (res.type == 'add_operator') {
                                            localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                            document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                            document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                        }
                                        else if (res.type == 'remove_operator') {
                                            localStorage.removeItem(_ProChatVariables_.storageOperator);
                                        }

                                        document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                    };

                                    this.socket.send(JSON.stringify({
                                        type: 'text_message',
                                        client: client.id,
                                        session: session.id,
                                        operator: operator != null ? operator.id : null,
                                        message: message
                                    }));

                                }
                                else {

                                    this.socket.send(JSON.stringify({
                                        type: 'text_message',
                                        client: client.id,
                                        session: session.id,
                                        operator: operator != null ? operator.id : null,
                                        message: message
                                    }));

                                }

                                let xhttp = new XMLHttpRequest();
                                xhttp.open('POST', `${_ProChatVariables_.urlApi}/sessionSendMessage`, true);
                                xhttp.setRequestHeader('Content-type', 'application/json');
                                xhttp.send(JSON.stringify({
                                    client: client.id,
                                    session: session.id,
                                    message: message
                                }));
                            }
                        }

                        document.getElementById('_ProChat_message').onfocus = () => {

                            if (this.socket.readyState > 1) {

                                this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                this.socket.onopen = function () {
                                    let xhttp = new XMLHttpRequest();
                                    xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                    xhttp.setRequestHeader('Content-type', 'application/json');
                                    xhttp.send(JSON.stringify({ session: session.id }));
                                }

                                this.socket.onmessage = (event) => {

                                    const res = JSON.parse(event.data);

                                    if (res.type == 'text_message') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span>${res.message}</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_start') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span class="_ProChat_typing">digitando...</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_finish') {
                                        document.getElementById('_ProChat_msg_tmp').remove();
                                    }
                                    else if (res.type == 'add_operator') {
                                        localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                        document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                        document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                    }
                                    else if (res.type == 'remove_operator') {
                                        localStorage.removeItem(_ProChatVariables_.storageOperator);
                                    }

                                    document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                };

                                this.socket.send(JSON.stringify({
                                    type: 'typing_start',
                                    client: client.id,
                                    session: session.id,
                                    operator: operator != null ? operator.id : null
                                }));

                            }
                            else {

                                this.socket.send(JSON.stringify({
                                    type: 'typing_start',
                                    client: client.id,
                                    session: session.id,
                                    operator: operator != null ? operator.id : null
                                }));
                            }
                        };

                        document.getElementById('_ProChat_message').onblur = () => {

                            if (this.socket.readyState > 1) {

                                this.socket = new WebSocket(`${_ProChatVariables_.urlSocket}/session?id=${session.id}`);

                                this.socket.onopen = function () {
                                    let xhttp = new XMLHttpRequest();
                                    xhttp.open('POST', `${_ProChatVariables_.urlApi}/setSessionOnline`, true);
                                    xhttp.setRequestHeader('Content-type', 'application/json');
                                    xhttp.send(JSON.stringify({ session: session.id }));
                                }

                                this.socket.onmessage = (event) => {

                                    const res = JSON.parse(event.data);

                                    if (res.type == 'text_message') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span>${res.message}</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_start') {

                                        element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin" id="_ProChat_msg_tmp">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span class="_ProChat_typing">digitando...</span>
                                            </span>
                                        `);

                                    }
                                    else if (res.type == 'typing_finish') {
                                        document.getElementById('_ProChat_msg_tmp').remove();
                                    }
                                    else if (res.type == 'add_operator') {
                                        localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                        document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                        document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                    }
                                    else if (res.type == 'remove_operator') {
                                        localStorage.removeItem(_ProChatVariables_.storageOperator);
                                    }

                                    document.getElementById('_ProChat_chat_form').scroll(0, document.getElementById('_ProChat_chat_form').scrollHeight);
                                };

                                this.socket.send(JSON.stringify({
                                    type: 'typing_finish',
                                    client: client.id,
                                    session: session.id,
                                    operator: operator != null ? operator.id : null
                                }));

                            }
                            else {

                                this.socket.send(JSON.stringify({
                                    type: 'typing_finish',
                                    client: client.id,
                                    session: session.id,
                                    operator: operator != null ? operator.id : null
                                }));
                            }
                        };

                        element.insertAdjacentHTML('beforeend', `
                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin _ProChat_chat_msg_item_login" id="_ProChat_form_msg">
                                <div class="_ProChat_chat_avatar">
                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                </div>
                                <span>Por favor, apresente-se no chat!</span>
                                <div>
                                    <form class="_ProChat_message_form">
                                        <input id="_ProChat_form_name" class="_ProChat_chat_input" placeholder="Seu nome" value="${session.name}" />
                                        <input id="_ProChat_form_phone" class="_ProChat_chat_input" placeholder="Seu telefone" value="${session.phone}" />
                                        <input id="_ProChat_form_email" class="_ProChat_chat_input" placeholder="Seu email" value="${session.email}" />
                                        <button type="button" id="_ProChat_sendFormSession" style="background:#ccc">&#127881; Obrigado</button>
                                    </form>
                                </div>
                            </span>
                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                <div class="_ProChat_chat_avatar">
                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                </div>
                                <span>${client.initial_message}</span>
                            </span>
                        `);

                        let xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                let res = JSON.parse(this.responseText);
                                if (res.success) {

                                    res.data.forEach(data => {

                                        if (data.sender == 'client' || data.sender == 'operator') {

                                            element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_admin">
                                                <div class="_ProChat_chat_avatar">
                                                    <img class="_ProChat_photo_client img-thumbnail" src="${client.photo}" />
                                                </div>
                                                <span>${data.message}</span>
                                            </span>
                                        `);
                                        }

                                        else {

                                            element.insertAdjacentHTML('beforeend', `
                                            <span class="_ProChat_chat_msg_item _ProChat_chat_msg_item_user" style="background:${client.widget_color1}">
                                                <span>${data.message}</span>
                                            </span>
                                        `);

                                        }

                                    });
                                }
                            }
                        };
                        xhttp.open('GET', `${_ProChatVariables_.urlApi}/sessionHistory/${session.id}`, true);
                        xhttp.send();

                        let xhttp2 = new XMLHttpRequest();
                        xhttp2.onreadystatechange = function () {
                            if (this.readyState == 4 && this.status == 200) {
                                let res = JSON.parse(this.responseText);
                                if (res.success) {
                                    if (res.data != null) {
                                        localStorage.setItem(_ProChatVariables_.storageOperator, JSON.stringify(res.data));
                                        document.getElementById('_ProChat_name_client').innerText = res.data.name;
                                        document.getElementById('_ProChat_title_client').innerText = res.data.title;
                                    }
                                }
                            }
                        };
                        xhttp2.open('GET', `${_ProChatVariables_.urlApi}/chatOperator/${client.id}/${session.id}`, true);
                        xhttp2.send();

                        document.getElementById('_ProChat_send_msg').style.display = 'block';

                    }

                }
                else {
                    throw new Error('Id inválido');
                }
            }
        };
        xhttp.open('GET', `${_ProChatVariables_.urlApi}/clientData/${this.client}`, true);
        xhttp.send();
    }
}

let _ProChat_Document = new ProChatDocument();

let ProChat = new _ProChat();
