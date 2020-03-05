<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('editModeBtns',[$this,'editModeBtns']),
            new TwigFunction('editModeModal',[$this,'editModeModal']),
            new TwigFunction('tinyMCE',[$this,'tinyMCE']),
            ];
    }

    /**
     * Displays the edit and remove buttons in edit mode
     * @param bool $edit
     * @return string
     */
    public function editModeBtns(bool $edit = null):?string {
        if($edit === true){
            return '<div class="block-edit"><i class="far fa-edit"></i><i class="far fa-trash-alt"></i></div>';
        }
        else{
            return null;
        }
    }

    /**
     * Add the classes for triggering the lightBox to the thumbnails if edit mode is disabled
     * @param bool|null $edit
     * @return string|null
     */
    public function editModeModal(bool $edit = null):?string {
        if($edit !== true){
            return 'data-toggle="modal" data-target="#carouselLightBox"';
        }
        else{
            return null;
        }
    }

    /**
     * Adds the TinyMCE script
     * @return string
     */
    public function tinyMCE():string {
        return
            '<script src="https://cdn.tiny.cloud/1/5wssjxvguotys7da1sjed9fyyrjavpfxmi2v8emgh1b9tx3i/tinymce/5/tinymce.min.js"'.
            ' referrerpolicy="origin"></script>'.
            '<script type="text/javascript">'.
            'tinymce.init({
                selector: \'.tiny-area\'
            });</script>';
    }

}