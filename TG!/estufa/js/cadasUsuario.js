class Validator{

    constructor(){
        this.validations = [
            'data-required',
            'data-min-length',
            'data-max-length',
            'data-email-validate',
            'data-only-letters',
            'data-equal',
            'data-password-validate',
        ]
    }

    //iniciar a validacao de todos os campos
    validate(form){

        // resgata toda as validacoes
        let currentValidations = document.querySelectorAll('form .error-validation');

        if(currentValidations.length > 0){
            this.cleanValidations(currentValidations);
        }

        //pegar os inputs
        let inputs = form.getElementsByTagName('input');

        //console.log(inputs);

        //transformo uma HTMLCollection -> array
        let inputsArray = [...inputs];

        //ver como esta o array
        //console.log(inputsArray);
        
        //loop nos inputs e validacao mediante ao que for encontrado
        inputsArray.forEach(function(input){
            
            //console.log(input);

            //loop em todas as validacoes existentes
            for(let i = 0; this.validations.length > i; i++){
                //verifica de a validacao atual existe no input
                if(input.getAttribute(this.validations[i]) != null){
                    //console.log('achou validacao');

                    //limpando a string para virar um metodo
                    let method = this.validations[i].replace('data-','').replace('-','');

                    //valor do input
                    let value = input.getAttribute(this.validations[i]);

                    //invocar o metodo
                    this[method](input,value);
                }
            }
        }, this);
    }

    //verifica se um input tem um numero minimo de caracteres
    minlength(input, minValue){

        let inputLength = input.value.length;

        let errorMessage = `O campo precisa ter pelo menos ${minValue} caracteres`;

        if(inputLength < minValue){
            this.printMessage(input, errorMessage);
        }

    }
    //verifica se um input passou do limite de caracteres
    maxlength(input, maxValue){

        let inputLength = input.value.length;

        let errorMessage = `O campo precisa ter menos ${maxValue} caracteres`;

        if(inputLength > maxValue){
            this.printMessage(input, errorMessage);
        }

    }

    //valida emails
    emailvalidate(input){

        let re = /\S+@\S+\.\S+/;

        let email = input.value;

        let errorMessage = 'Insira um e-mail no padrão usuario@email.com';

        if(!re.test(email)){
            this.printMessage(input, errorMessage);
        }
    }

    //valida se o campo tem apenas letras
    onlyletters(input){

        let re = /^[A-Za-z]+$/;

        let inputValue = input.value;

        let errorMessage = 'Este campo não aceita números nem caracteres especiais';

        if(!re.test(inputValue)) {
            this.printMessage(input, errorMessage);
        }

    }

    //metodo imprimir msg de erros
    printMessage(input, msg){

        //quantidade de erros

        let errorsQty = input.parentNode.querySelector('.error-validation');

        if(errorsQty === null){
            let template = document.querySelector('.error-validation').cloneNode(true);

            template.textContent = msg;

            let inputParent = input.parentNode;

            template.classList.remove('template');

            inputParent.appendChild(template);
        }

    }

    //verifica se o usuario e requerido
    required(input){

        let inputValue = input.value;

        if(inputValue === ''){
            let errorMessage = 'Este campo é obrigatório';

            this.printMessage(input, errorMessage);
        }

    }

    //verifica se o campo senha e igual a campo confirmar senha
    equal(input, inputName){

        let inputToCompare = document.getElementsByName(inputName)[0];

        let errorMessage = `Este campo precisa estar igual ao ${inputName}`;

        if(input.value != inputToCompare.value){
            this.printMessage(input, errorMessage);
        }
    }

    //regra de ter pelo menos um numero e letra maiuscula
    passwordvalidate(input){

        let charArr = input.value.split("");

        let uppercases = 0;
        let numbers = 0;

        for(let i = 0; charArr.length > i; i++){
            if(charArr[i] === charArr[i].toUpperCase() && isNaN(parseInt(charArr[i]))){
                uppercases++;
            }else if(!isNaN(parseInt(charArr[i]))){
                numbers++;
            }
        }
        //se nao houver numeros ou letras maiusculas
        if(uppercases === 0 || numbers === 0){
            let errorMessage = "A senha precisa de um caractere maiúsculo e um número";

            this.printMessage(input, errorMessage);
        }
    }


    //limpa as validacoes da tela
    cleanValidations(validations){
        validations.forEach(el => el.remove());
    }
}











let form = document.getElementById("register-form");
let submit = document.getElementById("btn-submit");

let validator = new Validator();

// evento que dispara as validações
submit.addEventListener('click',function(e) {

    e.preventDefault();
    //vai mapear todos os inputs
    validator.validate(form);
    
});