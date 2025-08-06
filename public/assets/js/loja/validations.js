// ===== VALIDAÇÕES E AUTCOMPLETE =====

class ValidationService {
    constructor() {
        this.init();
    }
    
    init() {
        this.initCepAutocomplete();
        this.initCpfValidation();
        this.initCnpjValidation();
        this.initEmailValidation();
        this.initPhoneValidation();
        this.initPasswordValidation();
    }
    
    // ===== CEP AUTOCOMPLETE =====
    initCepAutocomplete() {
        const cepInputs = document.querySelectorAll('input[name="cep"], input[data-cep]');
        
        cepInputs.forEach(input => {
            // Máscara do CEP
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) value = value.slice(0, 8);
                e.target.value = value.replace(/(\d{5})(\d{3})/, '$1-$2');
            });
            
            // Consulta CEP
            input.addEventListener('blur', (e) => {
                const cep = e.target.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    this.consultarCep(cep, input);
                }
            });
        });
    }
    
    async consultarCep(cep, input) {
        try {
            input.classList.add('loading');
            
            const response = await fetch(`/api/loja/cep/${cep}`);
            const data = await response.json();
            
            if (data.success) {
                this.preencherEndereco(data.data, input);
                this.showNotification('CEP encontrado!', 'success');
            } else {
                this.showNotification(data.message || 'CEP não encontrado', 'error');
            }
        } catch (error) {
            console.error('Erro ao consultar CEP:', error);
            this.showNotification('Erro ao consultar CEP', 'error');
        } finally {
            input.classList.remove('loading');
        }
    }
    
    preencherEndereco(data, cepInput) {
        const form = cepInput.closest('form');
        if (!form) return;
        
        // Preenche os campos automaticamente
        const campos = {
            'logradouro': data.logradouro,
            'bairro': data.bairro,
            'cidade': data.cidade,
            'estado': data.estado,
            'complemento': data.complemento || ''
        };
        
        Object.keys(campos).forEach(campo => {
            const input = form.querySelector(`[name="${campo}"]`);
            if (input && !input.value) {
                input.value = campos[campo];
                input.classList.add('filled');
            }
        });
        
        // Atualiza o campo estado se for um select
        const estadoSelect = form.querySelector('select[name="estado"]');
        if (estadoSelect) {
            estadoSelect.value = data.estado;
        }
    }
    
    // ===== CPF VALIDATION =====
    initCpfValidation() {
        const cpfInputs = document.querySelectorAll('input[name="cpf"], input[data-cpf]');
        
        cpfInputs.forEach(input => {
            // Máscara do CPF
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                e.target.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            });
            
            // Validação
            input.addEventListener('blur', (e) => {
                const cpf = e.target.value.replace(/\D/g, '');
                if (cpf.length === 11) {
                    if (this.validarCpf(cpf)) {
                        input.classList.remove('error');
                        input.classList.add('valid');
                    } else {
                        input.classList.remove('valid');
                        input.classList.add('error');
                        this.showNotification('CPF inválido', 'error');
                    }
                }
            });
        });
    }
    
    validarCpf(cpf) {
        // Remove caracteres não numéricos
        cpf = cpf.replace(/\D/g, '');
        
        // Verifica se tem 11 dígitos
        if (cpf.length !== 11) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cpf)) return false;
        
        // Calcula o primeiro dígito verificador
        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf[i]) * (10 - i);
        }
        let resto = soma % 11;
        let dv1 = resto < 2 ? 0 : 11 - resto;
        
        // Calcula o segundo dígito verificador
        soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf[i]) * (11 - i);
        }
        soma += dv1 * 2;
        resto = soma % 11;
        let dv2 = resto < 2 ? 0 : 11 - resto;
        
        // Verifica se os dígitos verificadores estão corretos
        return cpf[9] == dv1 && cpf[10] == dv2;
    }
    
    // ===== CNPJ VALIDATION =====
    initCnpjValidation() {
        const cnpjInputs = document.querySelectorAll('input[name="cnpj"], input[data-cnpj]');
        
        cnpjInputs.forEach(input => {
            // Máscara do CNPJ
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 14) value = value.slice(0, 14);
                e.target.value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
            });
            
            // Validação
            input.addEventListener('blur', (e) => {
                const cnpj = e.target.value.replace(/\D/g, '');
                if (cnpj.length === 14) {
                    if (this.validarCnpj(cnpj)) {
                        input.classList.remove('error');
                        input.classList.add('valid');
                    } else {
                        input.classList.remove('valid');
                        input.classList.add('error');
                        this.showNotification('CNPJ inválido', 'error');
                    }
                }
            });
        });
    }
    
    validarCnpj(cnpj) {
        // Remove caracteres não numéricos
        cnpj = cnpj.replace(/\D/g, '');
        
        // Verifica se tem 14 dígitos
        if (cnpj.length !== 14) return false;
        
        // Verifica se todos os dígitos são iguais
        if (/^(\d)\1+$/.test(cnpj)) return false;
        
        // Calcula o primeiro dígito verificador
        let soma = 0;
        const pesos1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for (let i = 0; i < 12; i++) {
            soma += parseInt(cnpj[i]) * pesos1[i];
        }
        
        let resto = soma % 11;
        let dv1 = resto < 2 ? 0 : 11 - resto;
        
        // Calcula o segundo dígito verificador
        soma = 0;
        const pesos2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        
        for (let i = 0; i < 13; i++) {
            soma += parseInt(cnpj[i]) * pesos2[i];
        }
        
        resto = soma % 11;
        let dv2 = resto < 2 ? 0 : 11 - resto;
        
        // Verifica se os dígitos verificadores estão corretos
        return cnpj[12] == dv1 && cnpj[13] == dv2;
    }
    
    // ===== EMAIL VALIDATION =====
    initEmailValidation() {
        const emailInputs = document.querySelectorAll('input[type="email"], input[name="email"]');
        
        emailInputs.forEach(input => {
            input.addEventListener('blur', (e) => {
                const email = e.target.value.trim();
                if (email && !this.validarEmail(email)) {
                    input.classList.add('error');
                    this.showNotification('E-mail inválido', 'error');
                } else {
                    input.classList.remove('error');
                }
            });
        });
    }
    
    validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    // ===== PHONE VALIDATION =====
    initPhoneValidation() {
        const phoneInputs = document.querySelectorAll('input[name="telefone"], input[name="phone"], input[data-phone]');
        
        phoneInputs.forEach(input => {
            // Máscara do telefone
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                if (value.length === 11) {
                    e.target.value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                } else if (value.length === 10) {
                    e.target.value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
                }
            });
            
            // Validação
            input.addEventListener('blur', (e) => {
                const phone = e.target.value.replace(/\D/g, '');
                if (phone.length >= 10 && phone.length <= 11) {
                    if (this.validarTelefone(phone)) {
                        input.classList.remove('error');
                        input.classList.add('valid');
                    } else {
                        input.classList.remove('valid');
                        input.classList.add('error');
                        this.showNotification('Telefone inválido', 'error');
                    }
                }
            });
        });
    }
    
    validarTelefone(telefone) {
        // Verifica se tem entre 10 e 11 dígitos
        if (telefone.length < 10 || telefone.length > 11) return false;
        
        // Verifica se começa com 9 (celular) ou 2-8 (fixo)
        if (telefone.length === 11) {
            return /^[6-9]/.test(telefone[2]);
        }
        
        return /^[2-8]/.test(telefone[2]);
    }
    
    // ===== PASSWORD VALIDATION =====
    initPasswordValidation() {
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        
        passwordInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                const password = e.target.value;
                const strength = this.calcularForcaSenha(password);
                
                // Remove classes anteriores
                input.classList.remove('weak', 'medium', 'strong');
                
                // Adiciona classe baseada na força
                if (password.length > 0) {
                    input.classList.add(strength);
                }
                
                // Atualiza indicador de força se existir
                const strengthIndicator = input.parentNode.querySelector('.password-strength');
                if (strengthIndicator) {
                    strengthIndicator.className = `password-strength ${strength}`;
                    strengthIndicator.textContent = this.getStrengthText(strength);
                }
            });
        });
    }
    
    calcularForcaSenha(senha) {
        let score = 0;
        
        // Comprimento
        if (senha.length >= 8) score += 1;
        if (senha.length >= 12) score += 1;
        
        // Caracteres
        if (/[a-z]/.test(senha)) score += 1;
        if (/[A-Z]/.test(senha)) score += 1;
        if (/[0-9]/.test(senha)) score += 1;
        if (/[^A-Za-z0-9]/.test(senha)) score += 1;
        
        if (score <= 2) return 'weak';
        if (score <= 4) return 'medium';
        return 'strong';
    }
    
    getStrengthText(strength) {
        const texts = {
            'weak': 'Fraca',
            'medium': 'Média',
            'strong': 'Forte'
        };
        return texts[strength] || '';
    }
    
    // ===== UTILITY FUNCTIONS =====
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="icon-${type === 'success' ? 'check' : type === 'error' ? 'x' : 'info'}"></i>
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove após 5 segundos
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
        
        // Fechar manualmente
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.addEventListener('click', () => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        });
    }
    
    // ===== FORM VALIDATION =====
    validateForm(form) {
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });
        
        return isValid;
    }
    
    // ===== REAL-TIME VALIDATION =====
    initRealTimeValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    if (input.classList.contains('error')) {
                        this.validateField(input);
                    }
                });
            });
            
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showNotification('Por favor, corrija os erros no formulário', 'error');
                }
            });
        });
    }
    
    validateField(input) {
        const value = input.value.trim();
        const type = input.type;
        const name = input.name;
        
        // Remove classes anteriores
        input.classList.remove('error', 'valid');
        
        // Validações específicas
        if (name === 'cpf' && value) {
            if (!this.validarCpf(value.replace(/\D/g, ''))) {
                input.classList.add('error');
                return false;
            }
        }
        
        if (name === 'cnpj' && value) {
            if (!this.validarCnpj(value.replace(/\D/g, ''))) {
                input.classList.add('error');
                return false;
            }
        }
        
        if (name === 'email' && value) {
            if (!this.validarEmail(value)) {
                input.classList.add('error');
                return false;
            }
        }
        
        if (name === 'telefone' && value) {
            if (!this.validarTelefone(value.replace(/\D/g, ''))) {
                input.classList.add('error');
                return false;
            }
        }
        
        // Validação de campo obrigatório
        if (input.hasAttribute('required') && !value) {
            input.classList.add('error');
            return false;
        }
        
        // Validação de tamanho mínimo
        const minLength = input.getAttribute('minlength');
        if (minLength && value.length < parseInt(minLength)) {
            input.classList.add('error');
            return false;
        }
        
        // Validação de tamanho máximo
        const maxLength = input.getAttribute('maxlength');
        if (maxLength && value.length > parseInt(maxLength)) {
            input.classList.add('error');
            return false;
        }
        
        // Se passou por todas as validações
        if (value) {
            input.classList.add('valid');
        }
        
        return true;
    }
}

// ===== INITIALIZE =====
document.addEventListener('DOMContentLoaded', function() {
    window.validationService = new ValidationService();
    window.validationService.initRealTimeValidation();
}); 