<?php

/**
 * Namespace
 */
namespace Person;

class ModulePersonList extends \Module
{

    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_personlist';

    /**
     * Template person entry
     *
     * @var string
     */
    protected $strTemplatePerson = 'person_list';

    /**
     * (non-PHPdoc)
     *
     * @see \Contao\Module::generate()
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate ('be_wildcard');

            $objTemplate->wildcard = '### PERSONEN LISTE ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if ($this->personTpl) {
            $this->strTemplatePerson = $this->personTpl;
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $objPeople = \PersonModel::findBy('pid', $this->person_archiv, array('order' => 'sorting ASC'));
        $arrSize = deserialize($this->imgSize);
        if ($objPeople) {
            $strHTML = '';
            while ($objPeople->next()) {
                $objTemplate = new \FrontendTemplate ($this->personTpl);
                $arrData = $this->getArrayOfPerson($objPeople, $arrSize);
                foreach ($arrData as $strName => $strValue) {
                    $objTemplate->$strName = $strValue;
                }
                \Controller::addImageToTemplate($objTemplate, $arrData);
                $strHTML .= $objTemplate->parse();
            }
        }

        $this->Template->strPeople = $strHTML;
    }

    /**
     * Return array of person
     *
     * @param object $objPerson
     * @param array $arrSize
     * @return array
     */
    protected function getArrayOfPerson($objPerson, $arrSize)
    {
        $arrData = $objPerson->row();
        $objFile = \FilesModel::findByPk($objPerson->image);
        $arrData ['singleSRC'] = $objFile->path;
        $arrData ['size'] = $arrSize;
        $arrData ['alt'] = $objPerson->firstname . ' ' . $objPerson->lastname;
        return $arrData;
    }
}
