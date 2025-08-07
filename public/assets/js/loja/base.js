/**
 * JavaScript Base - Loja Online
 * Funcionalidades essenciais para a loja
 */

// Configuração global
window.LojaApp = {
    // Configurações
    config: {
        apiUrl: '/api',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        theme: localStorage.getItem('theme') || 'default'
    },

    // Inicialização
    init() {
        this.setupEventListeners();
        this.setupTheme();
        this.setupCart();
        this.setupSearch();
        this.setupAnimations();
    },

    // Configurar event listeners
    setupEventListeners() {
        // Toggle do menu mobile
        const menuToggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('.nav');
        
        if (menuToggle && nav) {
            menuToggle.addEventListener('click', () => {
                nav.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });
        }

        // Botões de adicionar ao carrinho
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-add-cart')) {
                e.preventDefault();
                this.addToCart(e.target.dataset.productId, e.target.dataset.productName);
            }
        });

        // Botões de remover do carrinho
        document.addEventListener('click', (e) => {
            if (e.target.matches('.btn-remove-cart')) {
                e.preventDefault();
                this.removeFromCart(e.target.dataset.productId);
            }
        });

        // Toggle de tema
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Busca
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
        }
    },

    // Configurar tema
    setupTheme() {
        const theme = this.config.theme;
        document.body.className = `theme-${theme}`;
        
        // Atualizar ícone do tema
        const themeIcon = document.querySelector('.theme-icon');
        if (themeIcon) {
            themeIcon.className = `theme-icon ${theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'}`;
        }
    },

    // Alternar tema
    toggleTheme() {
        const currentTheme = this.config.theme;
        const newTheme = currentTheme === 'dark' ? 'default' : 'dark';
        
        this.config.theme = newTheme;
        localStorage.setItem('theme', newTheme);
        
        // Atualizar CSS
        const themeLink = document.querySelector('link[href*="temas/"]');
        if (themeLink) {
            themeLink.href = `/assets/css/loja/temas/${newTheme}.css`;
        }
        
        // Atualizar classe do body
        document.body.className = `theme-${newTheme}`;
        
        // Atualizar ícone
        const themeIcon = document.querySelector('.theme-icon');
        if (themeIcon) {
            themeIcon.className = `theme-icon ${newTheme === 'dark' ? 'fas fa-sun' : 'fas fa-moon'}`;
        }
    },

    // Configurar carrinho
    setupCart() {
        this.updateCartCount();
        this.loadCartFromStorage();
    },

    // Adicionar ao carrinho
    addToCart(productId, productName) {
        const cart = this.getCart();
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: productId,
                name: productName,
                quantity: 1
            });
        }
        
        this.saveCart(cart);
        this.updateCartCount();
        this.showNotification('Produto adicionado ao carrinho!', 'success');
    },

    // Remover do carrinho
    removeFromCart(productId) {
        const cart = this.getCart();
        const updatedCart = cart.filter(item => item.id !== productId);
        
        this.saveCart(updatedCart);
        this.updateCartCount();
        this.showNotification('Produto removido do carrinho!', 'info');
        
        // Recarregar página se estiver na página do carrinho
        if (window.location.pathname.includes('carrinho')) {
            location.reload();
        }
    },

    // Obter carrinho do localStorage
    getCart() {
        const cart = localStorage.getItem('cart');
        return cart ? JSON.parse(cart) : [];
    },

    // Salvar carrinho no localStorage
    saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
    },

    // Carregar carrinho do storage
    loadCartFromStorage() {
        const cart = this.getCart();
        if (cart.length > 0) {
            this.updateCartCount();
        }
    },

    // Atualizar contador do carrinho
    updateCartCount() {
        const cart = this.getCart();
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'block' : 'none';
        }
    },

    // Configurar busca
    setupSearch() {
        // Implementar busca em tempo real se necessário
    },

    // Manipular busca
    handleSearch(e) {
        const query = e.target.value.trim();
        if (query.length >= 2) {
            this.performSearch(query);
        }
    },

    // Realizar busca
    async performSearch(query) {
        try {
            const response = await fetch(`${this.config.apiUrl}/produtos/buscar?q=${encodeURIComponent(query)}`);
            const results = await response.json();
            this.displaySearchResults(results);
        } catch (error) {
            console.error('Erro na busca:', error);
        }
    },

    // Exibir resultados da busca
    displaySearchResults(results) {
        const searchResults = document.querySelector('.search-results');
        if (!searchResults) return;

        if (results.length === 0) {
            searchResults.innerHTML = '<p>Nenhum produto encontrado.</p>';
            return;
        }

        const html = results.map(product => `
            <div class="search-result-item">
                <img src="${product.imagem}" alt="${product.nome}">
                <div class="search-result-info">
                    <h4>${product.nome}</h4>
                    <p>R$ ${product.preco}</p>
                </div>
            </div>
        `).join('');

        searchResults.innerHTML = html;
    },

    // Configurar animações
    setupAnimations() {
        // Animar elementos quando entram no viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        });

        // Observar elementos com classe 'animate-on-scroll'
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    },

    // Mostrar notificação
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.textContent = message;
        
        // Adicionar ao DOM
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    },

    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Utilitários
    utils: {
        // Formatar preço
        formatPrice(price) {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(price);
        },

        // Formatar data
        formatDate(date) {
            return new Intl.DateTimeFormat('pt-BR').format(new Date(date));
        },

        // Validar email
        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        },

        // Validar CPF
        validateCPF(cpf) {
            cpf = cpf.replace(/[^\d]/g, '');
            if (cpf.length !== 11) return false;
            
            let sum = 0;
            for (let i = 0; i < 9; i++) {
                sum += parseInt(cpf.charAt(i)) * (10 - i);
            }
            let remainder = 11 - (sum % 11);
            if (remainder === 10 || remainder === 11) remainder = 0;
            if (remainder !== parseInt(cpf.charAt(9))) return false;
            
            sum = 0;
            for (let i = 0; i < 10; i++) {
                sum += parseInt(cpf.charAt(i)) * (11 - i);
            }
            remainder = 11 - (sum % 11);
            if (remainder === 10 || remainder === 11) remainder = 0;
            if (remainder !== parseInt(cpf.charAt(10))) return false;
            
            return true;
        }
    }
};

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    LojaApp.init();
});

// Expor para uso global
window.LojaApp = LojaApp;
