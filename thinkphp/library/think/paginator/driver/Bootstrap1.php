<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\paginator\driver;

use think\Paginator;

class Bootstrap1 extends Paginator
{
    
    public $rollPage  =5;//分页栏每页显示的页数

    public $showPage  =2;//总页数超过多少条时显示的首页末页

    protected function getTotal(){
        $html ='<span class="pagefy">共<strong>'.$this->total().'</strong>条数据</span>';
        return $html;
    }
    protected function getPageTotalShow(){
        $html ='<span class="gon">共<strong>'.$this->getPageTotal().'</strong>页</span><span>&nbsp;&nbsp;跳转至<span>';
        return $html;
    }
    protected function getPageTotal(){
        $pageTotal = ceil($this->total()/config('paginate.list_rows'));
        return $pageTotal;
    }
    protected function getIuput(){
        $html = '<form id="tp_page_controller_form" style="display:inline"><input class="tiaoz" type="text" name="page" id="tp_page_controller_input" value="'.input("page").'" /><span>&nbsp;页</span>&nbsp;&nbsp;<input class="pagefy" type="button" name="" id="tp_page_controller_submit" value="确定" /></form>';
        $html .= '<script>window.onload = function(){document.getElementById("tp_page_controller_submit").onclick = function(){document.getElementById("tp_page_controller_form").submit()}}</script>';
        return $html;
    }
    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton($text = "上一页")
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton($text = '下一页')
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 首页按钮
     * @param string $text
     * @return string
     */
    protected function getFirstButton($text = '首页')
    {
        if($this->lastPage > $this->showPage){
            if ($this->currentPage==1) {
                return $this->getDisabledTextWrapper($text);
            }
            $url = $this->url(1);

            return $this->getPageLinkWrapper($url, $text);
        }
    }

    /**
     * 末页按钮
     * @param string $text
     * @return string
     */
    protected function getLastButton($text = '末页')
    {
        if($this->lastPage > $this->showPage){
            if ($this->currentPage==$this->lastPage) {
                return $this->getDisabledTextWrapper($text);
            }
            $url = $this->url($this->lastPage);

            return $this->getPageLinkWrapper($url, $text);
        }
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks()
    {
        if ($this->simple)
            return '';

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null
        ];

        $rollPage = $this->rollPage;//分页栏每页显示的页数
        $nowPage = floor($rollPage/2);//计算分页临时变量

        if($this->lastPage <= $rollPage){
            $block['first'] = $this->getUrlRange(1, $this->lastPage);
        }else if($this->currentPage <= $nowPage){
            $block['first'] = $this->getUrlRange(1, $rollPage);
        }else if($this->currentPage >= ($this->lastPage - $nowPage)){
            $block['first'] = $this->getUrlRange($this->lastPage - $rollPage+1, $this->lastPage);
        }else{
            $block['first'] = $this->getUrlRange($this->currentPage - $nowPage, $this->currentPage + $nowPage);
        }
        $html = '';

        if (is_array($block['first'])) {
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['slider']);
        }

        if (is_array($block['last'])) {
            $html .= $this->getDots();
            $html .= $this->getUrlLinks($block['last']);
        }

        return $html;
    }

    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<div class="dataTables_paginate paging_simple_numbers"><ul class="pager">%s %s</ul></div>',
                    $this->getPreviousButton(),
                    $this->getNextButton()
                );
            } else {
                return sprintf(
                    '<div class="dataTables_paginate paging_simple_numbers"><ul class="pagination">%s %s %s %s %s %s %s</ul></div>',
                    $this->getFirstButton(),
                    $this->getPreviousButton(),
                    $this->getLinks(),
                    $this->getPageTotalShow(),
                    $this->getIuput(),
                    $this->getNextButton(),
                    $this->getLastButton()
                );
            }
        } else {
            return sprintf(
                '<ul class="pagination">%s</ul>',
                $this->getTotal()
            );
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getAvailablePageWrapper($url, $page)
    {
        return '<a href="' . htmlentities($url) . '" class="pagefy">' . $page . '</a>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper($text)
    {
        return '<a class="disabled pagefy">' . $text . '</a>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper($text)
    {
        return '<a class="active pagefy">' . $text . '</a>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots()
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls)
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  int    $page
     * @return string
     */
    protected function getPageLinkWrapper($url, $page)
    {
        $class = 'hidden-xs';
        if ($page == $this->currentPage()) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }
}
