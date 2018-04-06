set cindent
set autoindent "ai自动缩进
set smartindent "智能缩进
set nocompatible "关闭vi兼容
filetype plugin on "文件类型
set showmatch "括号匹配
set ruler "右下角显示光标状态行
set incsearch "设置快速搜索
set nobackup
set ts=4  
set sw=4  
set expandtab
set ru
set is
set nowrap
set nopaste
set pastetoggle=<f11>
syntax on

set fileencodings=utf-8,ucs-bom,gb18030,gbk,gb2312,cp936
set termencoding=utf-8
set encoding=utf-8

set autoindent
set backspace=2
set softtabstop=4

if has('gui_gtk2')
set guifont=Bitstream\ Vera\Sans\Mono\12
set guifont=Monaco\ 12
colorscheme desert
endif

autocmd BufReadPost *
\ if line("'\"") > 1 && line("'\"") <= line("$") |                                                                                                   
\ exe "normal! g'\"" |
\ endif

autocmd FileType php set omnifunc=phpcomplete#CompletePHP
