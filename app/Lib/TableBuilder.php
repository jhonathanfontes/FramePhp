<?php
// Lib/TableBuilder.php (AGORA GERANDO HTML PURO)

namespace App\Lib;

class TableBuilder
{
    protected $qtd = 15; // Quantidade de itens por página
    protected $headers_name; // Nomes dos cabeçalhos
    protected $headers_width; // Largura dos cabeçalhos (opcional)
    protected $has_checkbox = false; // Tem checkbox na primeira coluna?
    protected $has_img = false; // Tem coluna de imagem no final?
    protected $total; // Total de registros
    protected $msg_zero = "<i class='fas fa-file-search-outline pt-2 pb-2' style='font-size: 48px'></i><br><h6>Nenhuma informação encontrada!</h6>"; // Mensagem sem resultados
    protected $pg; // Página atual
    protected $npages; // Número total de páginas
    protected $inicioPg; // Índice inicial da página atual
    protected $fimPg; // Índice final da página atual
    protected $busca = []; // Parâmetros de busca (GET)
    protected $linhas = 0; // Contador de linhas adicionadas
    protected $data = []; // Dados brutos da tabela
    protected $link = ''; // Link da linha (para clique)
    protected array $col = []; // Colunas da linha atual
    protected $dataTableHtml = ""; // HTML acumulado da tabela

    public function __construct(int $qtd = 15)
    {
        $this->qtd = $qtd;
        $this->dataTableHtml = "";
        // return $this;
    }

    /**
     * Define se a primeira coluna terá checkboxes.
     */
    public function setHasCheckbox(bool $value): self
    {
        $this->has_checkbox = $value;
        return $this;
    }

    /**
     * Define se haverá uma coluna de imagem.
     * Necessário que a última coluna nos dados seja a URL da imagem.
     */
    public function setHasImage(bool $value): self
    {
        $this->has_img = $value;
        return $this;
    }

    /**
     * Define a quantidade de itens por página.
     */
    public function setQtd(int $qtd): self
    {
        if ($qtd > 0) {
            $this->qtd = $qtd;
        }
        return $this;
    }

    /**
     * Retorna a quantidade de itens por página.
     */
    public function getQtd(): int
    {
        return $this->qtd;
    }

    /**
     * Inicializa a tabela com dados e cabeçalhos.
     * @param array $data Array de dados (registros completos).
     * @param array $headers Array de strings para os nomes dos cabeçalhos.
     * @param array $getParams Parâmetros GET para manter na paginação.
     */
    public function init(array $data, array $headers, array $getParams = []): self
    {
        $this->headers_name = $headers;
        $this->busca = $getParams;
        $this->data = $data;
        $this->total = count($this->data);

        $this->pg = empty($_GET['pg']) ? 1 : (int)$_GET["pg"];

        $this->npages = floor($this->total / $this->qtd);
        if (($this->total % $this->qtd > 0) || ($this->npages == 0)) {
            $this->npages++;
        }

        if ($this->pg > $this->npages) {
            $this->pg = 1; // Reseta para a primeira página se a página atual for inválida
        }
        if ($this->pg < 1) { // Evita páginas negativas
            $this->pg = 1;
        }


        $this->inicioPg = ($this->pg - 1) * $this->qtd;
        $this->fimPg = $this->inicioPg + $this->qtd;

        $this->addHeader();
        return $this;
    }

    /**
     * Adiciona o cabeçalho da tabela.
     */
    private function addHeader()
    {
        $this->dataTableHtml .= "<div class='box pt-0 pb-0 mb-0'><div class='table-container responsive-table'>\n"; // Bulma .box
        $this->dataTableHtml .= "<table class='table is-striped is-hoverable is-fullwidth' id='manual-table'>\n"; // Bulma classes
        $this->dataTableHtml .= "<thead class='cf'>\n";
        $this->dataTableHtml .= "<tr>\n";

        $i = 0;
        if ($this->has_checkbox) {
            $this->dataTableHtml .= "<th class='form-check'> <input class='form-check-input' type='checkbox' id='ckbselectall' name='ckbselectall' onclick='selectAll(`checkbox`)'></th>\n";
            $i++;
        }

        foreach ($this->headers_name as $headerIndex => $headerName) {
            // Se o header_width for definido e tem índice correspondente
            $widthStyle = isset($this->headers_width[$headerIndex]) ? "style='width:" . $this->headers_width[$headerIndex] . "%'" : "";
            $this->dataTableHtml .= "<th {$widthStyle}>" . $headerName . "</th>\n";
        }
        $this->dataTableHtml .= "</tr>\n";
        $this->dataTableHtml .= "</thead>\n";
        $this->dataTableHtml .= "<tbody>\n"; // Abre o tbody aqui para addRow

        // Adiciona a mensagem de vazio se a consulta retorna vazio
        if ($this->total == 0) {
            $this->addNull();
        }
    }

    /**
     * Adiciona mensagem quando não há resultados.
     */
    private function addNull()
    {
        $colspan = count($this->headers_name);
        if ($this->has_checkbox) {
            $colspan++; // Se tiver checkbox, aumenta o colspan
        }
        $this->dataTableHtml .= "<tr>\n";
        $this->dataTableHtml .= "<td class=\"has-text-centered\" style=\"padding: 60px\" colspan='" . $colspan . "'>" . $this->msg_zero . "</td>\n";
        $this->dataTableHtml .= "</tr>\n";
    }

    /**
     * Adiciona o conteúdo de uma célula (para usar dentro de addRow).
     */
    public function addCol(string $content): self
    {
        $this->col[] = $content;
        return $this;
    }

    /**
     * Adiciona uma nova linha à tabela.
     * @param string $link URL para onde a linha deve redirecionar ao clicar.
     * @param string $onclick Função JS para executar ao clicar na linha.
     * @param string $style Estilos CSS adicionais para a linha.
     */
    public function addRow(string $link = "", string $onclick = "", string $style = ""): self
    {
        $this->link = ''; // Reseta o link/onclick para esta linha
        $rowStyle = "";

        if (!empty($link)) {
            $parameter = "&pg={$this->pg}"; // Mantém a página atual na URL
            foreach ($this->busca as $key => $value) {
                if (!empty($value) && $key != 'pg') { // Evita adicionar pg duas vezes
                    $parameter .= "&" . $key . "=" . urlencode((string)$value);
                }
            }
            $this->link = "onclick='location.href=`{$link}{$parameter}`'";
            $rowStyle = "style='cursor: pointer;{$style}'";
        }

        if (!empty($onclick)) {
            $this->link = "onclick='{$onclick}'";
            $rowStyle = "style='cursor: pointer;{$style}'";
        }

        $this->dataTableHtml .= "<tr id='linec" . $this->linhas . "' {$rowStyle}>\n";

        $j = 0;
        if ($this->has_checkbox) {
            // Assume que o primeiro item em $this->col é o ID ou valor do checkbox
            $checkboxValue = $this->col[0] ?? '';
            $this->dataTableHtml .= "<td data-label='Selecione' style='width:" . ($this->headers_width[0] ?? '') . "%'><div class=\"form-check\"><input type=\"checkbox\" value='{$checkboxValue}' id='c" . $this->linhas . "' class=\"form-check-input\"><label for='c" . $this->linhas . "'>&nbsp;</label></div></td>\n";
            $j++; // Incrementa para pular a coluna de checkbox
        }

        // Escreve os valores das colunas
        for ($colIdx = $j; $colIdx < count($this->col); $colIdx++) {
            $headerNameForDataTitle = ($this->headers_name[$colIdx] ?? '');
            $cellContent = $this->col[$colIdx];
            $cellStyle = isset($this->headers_width[$colIdx]) ? "style='width:" . $this->headers_width[$colIdx] . "%'" : "";

            // Lógica para a última coluna (imagem)
            if ($this->has_img && $colIdx == (count($this->headers_name) - 1)) { // Última coluna como imagem
                 $this->dataTableHtml .= "<td data-label='{$headerNameForDataTitle}' {$cellStyle}>" . $cellContent . "</td>\n";
            } else {
                 $this->dataTableHtml .= "<td data-label='{$headerNameForDataTitle}' {$cellStyle} {$this->link}>" . $cellContent . "</td>\n";
            }
        }

        $this->dataTableHtml .= "</tr>\n";
        $this->linhas++;
        $this->col = []; // Reseta as colunas para a próxima linha
        return $this;
    }

    /**
     * Define a largura dos cabeçalhos.
     * @param array $widths Array de inteiros ou strings (ex: [10, 20, 70] ou ["10%", "20px"]).
     */
    public function setHeadersWidth(array $widths): self
    {
        $this->headers_width = $widths;
        return $this;
    }

    /**
     * Conclui a construção da tabela e adiciona o rodapé (paginação).
     * @return string O HTML completo da tabela.
     */
    public function render(): string
    {
        $this->dataTableHtml .= "</tbody>\n"; // Fecha o tbody
        $this->dataTableHtml .= "</table></div>\n"; // Fecha a tabela e o table-container

        // Rodapé de Paginação Manual
        $this->dataTableHtml .= "<div class='box mt-0'>\n"; // Novo box para o rodapé
        $this->dataTableHtml .= "<form id='formTable' action='' method='get' style='margin:0'>\n";
        $this->dataTableHtml .= "<input type='hidden' id='pg' name='pg' value='" . $this->pg . "' />";
        foreach ($this->busca as $key => $value) {
            if ($key != 'pg') { // Evita duplicar o parâmetro pg
                $this->dataTableHtml .= "<input type='hidden' name='{$key}' value='" . htmlentities((string)$value ?? "") . "' />\n";
            }
        }
        $this->dataTableHtml .= "<div class=\"columns is-mobile is-vcentered\">\n"; // Usando colunas Bulma para o rodapé
        $this->dataTableHtml .= "<div class='column is-one-third is-size-7'><p class=\"has-text-weight-bold\">Total: " . $this->total . "</p></div>\n";
        $this->dataTableHtml .= "<div class='column is-one-third has-text-centered is-size-7'><p class=\"has-text-weight-bold\">Pg: " . $this->pg . "/" . $this->npages . "</p></div>\n";
        $this->dataTableHtml .= "<div class='column is-one-third has-text-right'>\n";
        $this->dataTableHtml .= "<nav class=\"pagination is-small\" role=\"navigation\" aria-label=\"pagination\">
                                    <a class=\"pagination-previous " . ($this->pg == 1 ? 'is-disabled' : '') . "\" href=\"javascript:void(0)\" onclick=\"formTableAction('" . ($this->pg - 1) . "')\">Anterior</a>
                                    <a class=\"pagination-next " . ($this->pg == $this->npages ? 'is-disabled' : '') . "\" href=\"javascript:void(0)\" onclick=\"formTableAction('" . ($this->pg + 1) . "')\">Próxima</a>
                                    <ul class=\"pagination-list\">
                                        <li><a class=\"pagination-link is-current\" aria-label=\"Página {$this->pg}\" aria-current=\"page\">{$this->pg}</a></li>
                                    </ul>
                                </nav>\n";
        $this->dataTableHtml .= "</div></div>\n";
        $this->dataTableHtml .= "</form>\n";
        $this->dataTableHtml .= "</div>\n"; // Fecha o box do rodapé

        // Script JS para a paginação manual
        $this->dataTableHtml .= "<script>
                function formTableAction(pg){
                    document.getElementById('pg').value = pg;
                    document.getElementById('formTable').submit();
                }
                function selectAll(className) { // Funcao selectAll para checkboxes
                    var checkboxes = document.querySelectorAll('.' + className + ' .form-check-input');
                    var selectAllCheckbox = document.getElementById('ckbselectall');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                }
                </script>";
        $this->dataTableHtml .= "</div>\n"; // Fecha o div inicial

        return $this->dataTableHtml;
    }

    /**
     * Verifica se a linha atual está na página a ser exibida.
     * Usado no loop foreach do controlador.
     * @param int $index O índice da linha no array de dados brutos.
     */
    public function checkPagination(int $index): bool
    {
        return ($index >= $this->inicioPg) && ($index < $this->fimPg);
    }
}