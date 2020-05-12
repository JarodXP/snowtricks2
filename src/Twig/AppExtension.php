<?php

declare(strict_types=1);


namespace App\Twig;

use App\Entity\Media;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class AppExtension
 * Adds specific functions and filers to the Twig environment
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        parent::getFilters();

        return [
            new TwigFilter('userRole', [$this,'displayUserRole']),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        parent::getFunctions();

        return [
            new TwigFunction('editModeModal', [$this,'editModeModal']),
            new TwigFunction('avatarFilename', [$this,'getAvatarFilename']),
            new TwigFunction('tinyMCE', [$this,'tinyMCE']),
            new TwigFunction('editButtons', [$this, 'editButtons']),
            ];
    }

    ///////////// FILTERS /////////////

    /**
     * Transforms the Symfony user role into a user friendly string
     * @param string $userRole
     * @return string
     */
    public function displayUserRole(string $userRole):string
    {
        return ucfirst(strtolower(explode('_', $userRole)[1]));
    }

    ///////////// FUNCTIONS /////////////

    /**
     * Add the classes for triggering the lightBox to the thumbnails if edit mode is disabled
     * @param bool|null $edit
     * @return string|null
     */
    public function editModeModal(bool $edit = null):?string
    {
        if ($edit !== true) {
            return 'data-toggle="modal" data-target="#carouselLightBox"';
        } else {
            return null;
        }
    }

    /**
     * Gets the user avatar filename or sets the default avatar
     * @param Media|null $avatar
     * @return string
     */
    public function getAvatarFilename(?Media $avatar):string
    {
        if (is_null($avatar)) {
            return 'default_avatar.jpg';
        }

        return $avatar->getFileName();
    }

    /**
     * Adds the TinyMCE script
     * @return string
     */
    public function tinyMCE():string
    {
        return
            '<script src="https://cdn.tiny.cloud/1/5wssjxvguotys7da1sjed9fyyrjavpfxmi2v8emgh1b9tx3i/tinymce/5/tinymce.min.js"'.
            ' referrerpolicy="origin"></script>'.
            '<script type="text/javascript">'.
            'tinymce.init({
                selector: \'.tiny-area\'
            });</script>';
    }

    /**
     * Renders the Edit Block with the edit and trash icons and their own links
     * @param string $editPath
     * @param string $removePath
     * @param bool $disableEdit
     * @param bool $disableRemove
     * @return string
     */
    public function editButtons(string $editPath, string $removePath, bool $disableEdit = false, bool $disableRemove = false)
    {
        //Builds the links with the corresponding icons
        $editLink = '<a href="'.$editPath.'"><i class="far fa-edit"></i></a>';
        $removeLink = '<a href="'.$removePath.'"><i class="far fa-trash-alt"></i></a>';

        //Possibly disables the specified links
        if ($disableEdit === true) {
            $editLink = '<a class="disabled"><i class="far fa-edit"></i></a>';
        }
        if ($disableRemove === true) {
            $removeLink = '<a class="disabled"><i class="far fa-trash-alt"></i></a>';
        }

        //Returns the whole block
        return
            '<div class="block-edit">'.$editLink.$removeLink.'</div>';
    }
}
