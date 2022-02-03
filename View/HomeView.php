<?php
class HomeView extends View
{

    public function __construct()
    {
        $this->statview = new StationView();

        parent::__construct();
    }

    public function constructHead($switch, $datas, $info, $liveStation)
    {
        require $this->file_admin;

        $param = array(
            "META_KEY" => $this->l->trad('META_KEY'),
            "META_DESCRIPTION" => $this->l->trad('META_DESCRIPTION'),
            "MBELL_TITRE" => $this->l->trad('MBELL_TITRE_HOME'),
            "_CSS" => $this->getCSS($switch, $datas, $info, $liveStation),
            "_LOGO" => "1",
            "_ROOT" => $this->getRoot(),
            "_LG" => $this->l->getLg(),
            "_DRAP" => $this->langDrapMenu($this->l->lgList(), $this->l->getLg()),
        );
        $this->page =  $this->getHead($param);
        $this->page .=  '<body id="body_home">';
        $this->page .= $this->getMenu($param, $switch);
        $this->page .= $this->getBurger();

        $this->page .= $this->getHeader($param);
    }


    //datas = api datas
    //info = stat_type
    public function displayHome($info, $datas, $config, $tab, $switch, $liveStation)
    {
        $param = array(
            "_LG" => $this->l->getLg(),
        );

        $this->constructHead($switch, $datas, $info, $liveStation);
        $this->page .= '<main id="main_index">';
        $this->page .= '<section class="container-fluid" id="home_title">';
        $this->page .= $this->getTitle($param, $datas, $info, $liveStation);
        $this->page .= '</section>';
        $this->page .= '<section class="container-fluid" id="home_main">';
        $this->page .= '<form id="formdmy" method="POST" action="index.php?controller=home&action=s_dmy&lg=' . $param['_LG'] . '">';
        $this->page .= '<input id="inputdmy" name="s_dmy" type="hidden">';
        $this->page .= $this->getTabHome($param, $tab, $switch, $config, $datas, $info, $liveStation);
        $this->page .= '</form>';
        $this->page .= '</section>';
        $this->page .= '</main>';

        $this->display();
        if ($info['stat_type'] == 'live' || $info['stat_type'] == 'weewx') {
            echo '<style>
           /*on supprime le tab_down */
           .tab_down{
               display:none;
            }
           .tab_mid{
               margin-bottom: 8px;
            }
            /*on supprime le DMY */
            .dmy {
                display:none;
            }
    .dmy_arrow_right.hover_arrow_right{
        background:none;
    }
    .dmy_arrow_left.hover_arrow_left{
        background:none;
    }
            </style>';
        }
    }


    public function getTabHome($param, $tab, $switch, $config, $datas, $info, $liveStation)
    {
        $page = '';
        for ($i = 1; $i <= $tab['tab_lines']; $i++) {
            $page .=  $this->addRow($param, $tab, $i, $switch, $config, $datas, $info, $liveStation);
        }
        return $page;
    }


    public function addRow($param, $tab, $row_id, $switch, $config, $datas, $info, $liveStation)
    {
        $page = '<div class="row row_espace row_espace_top" id="tab_row_' . $row_id . '">';
        $page .= $this->getInc($param, $tab, $row_id, 'a',  $switch, $config, $datas, $info, $liveStation);
        $page .= $this->getInc($param, $tab, $row_id, 'b', $switch, $config, $datas, $info, $liveStation);
        $page .= $this->getInc($param, $tab, $row_id, 'c', $switch, $config, $datas, $info, $liveStation);
        $page .= '</div>';
        return $page;
    }




    public function getInc($param, $tab, $row_id, $select_id, $switch, $config, $datas, $info, $liveStation)
    {
        $tab_n_abc = 'tab_' . $row_id . $select_id;
        $ntab = $tab[$tab_n_abc];

        switch ($ntab) {
            case '0':
                $up = $this->modUp1('0', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('0', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('0', $datas, $info, $liveStation);
                break;
            case '1':
                $up = $this->modUp1('1', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('1', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('1', $datas, $info, $liveStation);
                break;
            case '2':
                $up = $this->modUp2('2', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('2', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('2', $switch, $datas, $info, $liveStation);
                break;
            case '3':
                $up = $this->modUp1('3', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('3', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown7($config, $tab, $datas, $info, $liveStation);
                break;
            case '4':
                $up = ($this->statview->is_tab($tab, '7') == true) ? $this->modUp1('4', $config, $switch, $datas, $info, $liveStation) : $this->modUp2('4', $config, $switch, $datas, $info, $liveStation);
                $mid = ($this->statview->is_tab($tab, '7') == true) ? $this->modMid1('4', $config, $switch, $datas, $info, $liveStation) : $this->modMid2('4', $switch, $config, $datas, $info, $liveStation);
                $down = ($this->statview->is_tab($tab, '7') == true) ? $this->modDown1('4', $datas, $info, $liveStation) : $this->modDown3($param, $tab, $tab_n_abc, $switch, $config, $datas, $info, $liveStation);
                break;
            case '5':
                $up =  $this->modUp2('5', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('5', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown4($switch, $datas, $info, $liveStation);
                break;
            case '6':
                $up = ($this->statview->is_tab($tab, '13') == false && $this->statview->is_tab($tab, '14') == false) ? $this->modUp1('6', $config, $switch, $datas, $info, $liveStation) : $this->modUp1('45', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1($ntab, $config, $switch, $datas, $info, $liveStation);
                $down = ($this->statview->is_tab($tab, '13') == false && $this->statview->is_tab($tab, '14') == false) ? $this->modDown5('6', $config, $switch, $datas, $info, $liveStation) : $this->modDown1('6', $datas, $info, $liveStation);
                break;
            case '7':
                $up = $this->modUp1('7', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('7', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('7', $datas, $info, $liveStation);
                break;
            case '8':
                $up = $this->modUp3($switch, $config, $tab, $datas, $info, $liveStation);
                $mid = $this->modMid3($switch, $config, $tab, $datas, $info, $liveStation);
                $down = $this->modDown6($config, $tab, $datas, $info, $liveStation);
                break;
            case '9':
                $up = $this->modUp1('9', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('9', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown5('9', $config, $switch, $datas, $info, $liveStation);
                break;
            case '10':
                $up = $this->modUp2('10', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid4('10', $switch, $datas, $info, $liveStation);
                $down = $this->modDown2('10', $switch, $datas, $info, $liveStation);
                break;
            case '11':
                $up = $this->modUp2('11', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('11', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('11', $switch, $datas, $info, $liveStation);
                break;
            case '12':
                $up = $this->modUp2('12', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('12', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('12', $switch, $datas, $info, $liveStation);
                break;
            case '13':
                $up = $this->modUp1('13', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('13', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('13', $datas, $info, $liveStation);
                break;
            case '14':
                $up = $this->modUp1('14', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('14', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('14', $datas, $info, $liveStation);
                break;
            case '15':
                $up = $this->modUp2('15', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('15', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('15', $switch, $datas, $info, $liveStation);
                break;
            case '16':
                $up = $this->modUp2('16', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('16', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('16', $switch, $datas, $info, $liveStation);
                break;
            case '17':
                $up = $this->modUp2('17', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('17', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('17', $switch, $datas, $info, $liveStation);
                break;
            case '18':
                $up = $this->modUp2('18', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('18', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('18', $switch, $datas, $info, $liveStation);
                break;
            case '19':
                $up = $this->modUp2('19', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('19', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('19', $switch, $datas, $info, $liveStation);
                break;
            case '20':
                $up = $this->modUp2('20', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('20', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('20', $switch, $datas, $info, $liveStation);
                break;
            case '21':
                $up = $this->modUp2('21', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('21', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('21', $switch, $datas, $info, $liveStation);
                break;
            case '22':
                $up = $this->modUp1('22', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid5('22', $switch, $datas, $info, $liveStation);
                $down = $this->modDown5('22', $config, $switch, $datas, $info, $liveStation);
                break;
            case '23':
                $up = $this->modUp1('23', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid5('23', $switch, $datas, $info, $liveStation);
                $down = $this->modDown5('23', $config, $switch, $datas, $info, $liveStation);
                break;
            case '24':
                $up = $this->modUp2('24', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('24', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('24', $switch, $datas, $info, $liveStation);
                break;
            case '25':
                $up = $this->modUp2('25', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('25', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('25', $switch, $datas, $info, $liveStation);
                break;
            case '26':
                $up = $this->modUp2('26', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('26', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('26', $switch, $datas, $info, $liveStation);
                break;
            case '27':
                $up = $this->modUp2('27', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('27', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('27', $switch, $datas, $info, $liveStation);
                break;
            case '28':
                $up = $this->modUp2('28', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('28', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('28', $switch, $datas, $info, $liveStation);
                break;
            case '29':
                $up = $this->modUp2('29', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('29', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('29', $switch, $datas, $info, $liveStation);
                break;
            case '30':
                $up = $this->modUp2('30', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('30', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('30', $switch, $datas, $info, $liveStation);
                break;
            case '31':
                $up = $this->modUp2('31', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('31', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('31', $switch, $datas, $info, $liveStation);
                break;
            case '32':
                $up = $this->modUp2('32', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('32', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('32', $switch, $datas, $info, $liveStation);
                break;
            case '33':
                $up = $this->modUp2('33', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('33', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('33', $switch, $datas, $info, $liveStation);
                break;
            case '34':
                $up = $this->modUp2('34', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('34', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('34', $switch, $datas, $info, $liveStation);
                break;
            case '35':
                $up = $this->modUp2('35', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('35', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('35', $switch, $datas, $info, $liveStation);
                break;
            case '36':
                $up = $this->modUp2('36', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('36', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('36', $switch, $datas, $info, $liveStation);
                break;
            case '37':
                $up = $this->modUp2('37', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('37', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('37', $switch, $datas, $info, $liveStation);
                break;
            case '38':
                $up = $this->modUp2('38', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('38', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('38', $switch, $datas, $info, $liveStation);
                break;
            case '39':
                $up = $this->modUp2('39', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('39', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('39', $switch, $datas, $info, $liveStation);
                break;
            case '40':
                $up = $this->modUp2('40', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('40', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('40', $switch, $datas, $info, $liveStation);
                break;
            case '41':
                $up = $this->modUp2('41', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('41', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('41', $switch, $datas, $info, $liveStation);
                break;
            case '42':
                $up = $this->modUp2('42', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('42', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('42', $switch, $datas, $info, $liveStation);
                break;
            case '43':
                $up = $this->modUp2('43', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('43', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('43', $switch, $datas, $info, $liveStation);
                break;
            case '44':
                $up = $this->modUp2('44', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid2('44', $switch, $config, $datas, $info, $liveStation);
                $down = $this->modDown2('44', $switch, $datas, $info, $liveStation);
                break;
            case '46':
                $up = $this->modUp1('46', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('46', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('46', $datas, $info, $liveStation);
                break;
            default:
                $up = $this->modUp1('0', $config, $switch, $datas, $info, $liveStation);
                $mid = $this->modMid1('0', $config, $switch, $datas, $info, $liveStation);
                $down = $this->modDown1('0', $datas, $info, $liveStation);
                break;
        }




        $page = '<div id="tabref_' . $tab_n_abc . '" class="col-4 px-0 px-sm-1 px-md-2 px-lg-4 px-xl-8">';
        $page .= '<div id="tabval_' . $ntab . '" class="tab_inc">';
        $page .= $up;
        $page .= $mid;
        $page .= $down;
        $page .= '</div>';
        $page .= '</div>';

        return $page;
    }



    public function modUp1($ntab, $config, $switch, $datas, $info, $liveStation)
    {
        $valArray = $this->statview->incUp1($datas, $switch, $config, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('up_1', 'inc');
        $page = str_replace('{ICON_TOOLTIP}',  $valArray['ICON_TOOLTIP'], $page);
        $page = str_replace('{ICON}',  $valArray['ICON'], $page);
        $page = str_replace('{H2_TXT}',  $valArray['H2_TXT'], $page);
        $page = str_replace('{H2_TEXT}',  $valArray['H2_TEXT'], $page);
        return $page;
    }

    public function modUp2($ntab, $config, $switch, $datas, $info, $liveStation)
    {

        $valArray = $this->statview->incUp1($datas, $switch, $config, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('up_2', 'inc');
        $page = str_replace('{ICON_TOOLTIP}',  $valArray['ICON_TOOLTIP'], $page);
        $page = str_replace('{ICON}',  $valArray['ICON'], $page);
        $page = str_replace('{H2_TXT}',  $valArray['H2_TXT'], $page);
        $page = str_replace('{H2_TEXT}',  $valArray['H2_TEXT'], $page);
        $page = str_replace('{_DMY_ARROW_LEFT}',  $this->dmyArrow('left', $switch), $page);
        $page = str_replace('{_DMY_ARROW_RIGHT}',  $this->dmyArrow('right', $switch), $page);
        $page = str_replace('{_DMY_1}',  $this->dmy1($switch), $page);
        return $page;
    }


    //Up Unique Sun-UV
    public function modUp3($switch, $config, $tab, $datas, $info, $livestation)
    {
        $page = $this->searchHTML('up_3', 'inc');
        $page = str_replace('{UP_SPECIAL}',  $this->statview->incUpSun($switch, $config, $tab, $datas, $info, $livestation), $page);
        return $page;
    }

    public function modMid1($ntab, $config, $switch, $datas, $info, $liveStation)
    {
        $valArray = $this->statview->incMid1($datas, $switch, $config, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('mid_1', 'inc');
        $page = str_replace('{_VALUE_MAIN}',  $valArray['_VALUE_MAIN'], $page);
        $page = str_replace('{_UNIT}',  $valArray['_UNIT'], $page);
        $page = str_replace('{_CLASS_UNIT_SMALL}',  $valArray['_CLASS_UNIT_SMALL'], $page);
        $page = str_replace('{_CLASS_UNIT_LARGE}',  $valArray['_CLASS_UNIT_LARGE'], $page);
        $page = str_replace('{_color}',  $valArray['color'], $page);
        return $page;
    }

    public function modMid2($ntab, $switch, $config, $datas, $info, $liveStation)
    {
        $valArray = $this->statview->incMid1($datas, $switch, $config, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('mid_2', 'inc');
        $page = str_replace('{_VALUE_MAIN}',  $valArray['_VALUE_MAIN'], $page);
        $page = str_replace('{_UNIT}',  $valArray['_UNIT'], $page);
        $page = str_replace('{_CLASS_UNIT_SMALL}',  $valArray['_CLASS_UNIT_SMALL'], $page);
        $page = str_replace('{_CLASS_UNIT_LARGE}',  $valArray['_CLASS_UNIT_LARGE'], $page);
        $page = str_replace('{_color}',  $valArray['color'], $page);
        $page = str_replace('{_DMY_2}',  $this->dmy2($switch), $page);
        return $page;
    }

    //Mid Unique Sun-UV
    public function modMid3($switch, $config, $tab, $datas, $info, $liveStation)
    {
        $page = $this->searchHTML('mid_3', 'inc');
        $page = str_replace('{MID_SPECIAL}',  $this->statview->incMidSun($switch, $config, $tab, $datas, $info, $liveStation), $page);
        return $page;
    }

    public function modMid4($ntab, $switch, $datas, $info, $liveStation)
    {

        $valArray = $this->statview->incMid2($datas, $switch, $info, $liveStation)[$ntab];
        $page = $this->searchHTML('mid_4', 'inc');
        $page = str_replace('{TEXT_TOOLTIP_S}',  $valArray['TEXT_TOOLTIP_S'], $page);
        $page = str_replace('{TEXT_TOOLTIP_M}',  $valArray['TEXT_TOOLTIP_M'], $page);
        $page = str_replace('{TEXT_TOOLTIP_L}',  $valArray['TEXT_TOOLTIP_L'], $page);
        $page = str_replace('{_CLASS_UNIT_SMALL}',  $valArray['_CLASS_UNIT_SMALL'], $page);
        $page = str_replace('{_CLASS_UNIT_MIDDLE}',  $valArray['_CLASS_UNIT_MIDDLE'], $page);
        $page = str_replace('{_CLASS_UNIT_LARGE}',  $valArray['_CLASS_UNIT_LARGE'], $page);
        $page = str_replace('{TXT_ALTERN}',  $valArray['TXT_ALTERN'], $page);
        $page = str_replace('{_VALUE_MAIN}',  $valArray['_VALUE_MAIN'], $page);
        $page = str_replace('{_UNIT_S}',  $valArray['_UNIT_S'], $page);
        $page = str_replace('{_UNIT_M}',  $valArray['_UNIT_M'], $page);
        $page = str_replace('{_UNIT_L}',  $valArray['_UNIT_L'], $page);
        $page = str_replace('{_color}',  $valArray['color'], $page);
        $page = str_replace('{_DMY_2}',  $this->dmy2($switch), $page);
        return $page;
    }

    public function modMid5($ntab, $switch, $datas, $info, $liveStation)
    {

        $valArray = $this->statview->incMid3($datas, $switch, $info, $liveStation)[$ntab];
        $page = $this->searchHTML('mid_5', 'inc');
        $page = str_replace('{TEXT_TOOLTIP_S}',  $valArray['TEXT_TOOLTIP_S'], $page);
        $page = str_replace('{TEXT_TOOLTIP_L}',  $valArray['TEXT_TOOLTIP_L'], $page);
        $page = str_replace('{_CLASS_UNIT_SMALL}',  $valArray['_CLASS_UNIT_SMALL'], $page);
        $page = str_replace('{_CLASS_UNIT_LARGE}',  $valArray['_CLASS_UNIT_LARGE'], $page);
        $page = str_replace('{_VALUE_MAIN}',  $valArray['_VALUE_MAIN'], $page);
        $page = str_replace('{_UNIT_S}',  $valArray['_UNIT_S'], $page);
        $page = str_replace('{_UNIT_L}',  $valArray['_UNIT_L'], $page);
        $page = str_replace('{_color}',  $valArray['color'], $page);
        return $page;
    }

    public function modDown1($ntab, $datas, $info, $liveStation)
    {
        $valArray = $this->statview->incDown1($datas, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('down_1', 'inc');
        $page = str_replace('{_VALUE_DOWN_S}',  $valArray['_VALUE_DOWN_S'], $page);
        $page = str_replace('{_VALUE_DOWN_L}',  $valArray['_VALUE_DOWN_L'], $page);
        $page = str_replace('{CSS_DOWN}',  $valArray['CSS_DOWN'], $page);
        return $page;
    }


    public function modDown2($ntab, $switch, $datas, $info, $livestation)
    {

        $valArray = $this->statview->incDown2($datas, $switch, $info, $livestation)[$ntab];

        $page = $this->searchHTML('down_2', 'inc');

        $page = str_replace('{_DMY_3}',  $this->dmy3($switch), $page);
        $page = str_replace('{_DMY_4}',  $this->dmy1($switch), $page);
        $page = str_replace('{CSS_DOWN}',  $valArray['CSS_DOWN'], $page);
        $page = str_replace('{TEXT_DOWN_SMALL_n}',  $valArray['TEXT_DOWN_SMALL_n'], $page);
        $page = str_replace('{TEXT_DOWN_LARGE_n}',  $valArray['TEXT_DOWN_LARGE_n'], $page);
        $page = str_replace('{TEXT_DOWN_SMALL_x}',  $valArray['TEXT_DOWN_SMALL_x'], $page);
        $page = str_replace('{TEXT_DOWN_LARGE_x}',  $valArray['TEXT_DOWN_LARGE_x'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_SMALL}',  $valArray['CLASS_UNIT_DOWN_SMALL'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_LARGE}',  $valArray['CLASS_UNIT_DOWN_LARGE'], $page);
        $page = str_replace('{_UNIT_DOWN_SMALL}',  $valArray['_UNIT_DOWN_SMALL'], $page);
        $page = str_replace('{_UNIT_DOWN_LARGE}',  $valArray['_UNIT_DOWN_LARGE'], $page);
        $page = str_replace('{DMY_LETTER}',  $this->dmyLetter($switch), $page);
        $page = str_replace('{DMY_OF_DOWN_n}',  $valArray['DMY_OF_DOWN_n'], $page);
        $page = str_replace('{DMY_OF_DOWN_x}',  $valArray['DMY_OF_DOWN_x'], $page);
        $page = str_replace('{DMY_TXT_TOOLTIP}',  $valArray['DMY_TXT_TOOLTIP'], $page);
        $page = str_replace('{_DMY_VALUE_n}',  $valArray['_DMY_VALUE_n'], $page);
        $page = str_replace('{_DMY_VALUE_x}',  $valArray['_DMY_VALUE_x'], $page);
        return $page;
    }

    public function modDown3($param, $tab, $tab_n_abc, $switch, $config, $datas, $info, $liveStation)
    {
        $ntab = $tab[$tab_n_abc];
        $valArray = $this->statview->incDown3($datas, $switch, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('down_3', 'inc');
        $page = str_replace('{_LG}',  $param['_LG'], $page);
        $page = str_replace('{_DMY_3}',  $this->dmy3($switch), $page);
        $page = str_replace('{_DMY_4}',  $this->dmy1($switch), $page);
        $page = str_replace('{CSS_DOWN}',  $valArray['CSS_DOWN'], $page);
        $page = str_replace('{TEXT_DOWN_SMALL}',  $valArray['TEXT_DOWN_SMALL'], $page);
        $page = str_replace('{TEXT_DOWN_LARGE}',  $valArray['TEXT_DOWN_LARGE'], $page);
        $page = str_replace('{ALTERN_TXT_S_1}',  $valArray['ALTERN_TXT_S_1'], $page);
        $page = str_replace('{ALTERN_TXT_S_2}',  $valArray['ALTERN_TXT_S_2'], $page);
        $page = str_replace('{ALTERN_TXT_S_3}',  $valArray['ALTERN_TXT_S_3'], $page);
        $page = str_replace('{ALTERN_TXT_L_1}',  $valArray['ALTERN_TXT_L_1'], $page);
        $page = str_replace('{ALTERN_TXT_L_2}',  $valArray['ALTERN_TXT_L_2'], $page);
        $page = str_replace('{ALTERN_TXT_L_3}',  $valArray['ALTERN_TXT_L_3'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_SMALL}',  $valArray['CLASS_UNIT_DOWN_SMALL'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_LARGE}',  $valArray['CLASS_UNIT_DOWN_LARGE'], $page);
        $page = str_replace('{_UNIT_DOWN_SMALL}',  $valArray['_UNIT_DOWN_SMALL'], $page);
        $page = str_replace('{_UNIT_DOWN_LARGE}',  $valArray['_UNIT_DOWN_LARGE'], $page);
        $page = str_replace('{DMY_OF_DOWN}',  $valArray['DMY_OF_DOWN'], $page);
        $page = str_replace('{_DMY_VALUE}',  $valArray['_DMY_VALUE'], $page);
        return $page;
    }

    //Down Unique Heat-Windchill
    public function modDown4($switch, $datas, $info, $liveStation)
    {
        $page = $this->searchHTML('down_4', 'inc');
        $page = str_replace('{_DMY_3}',  $this->dmy3($switch), $page);
        $page = str_replace('{_DMY_4}',  $this->dmy1($switch), $page);
        $page = str_replace('{DMY_SPECIAL}',  $this->statview->downHeatWind($switch, $datas, $info, $liveStation), $page);
        return $page;
    }


    public function modDown5($ntab, $config, $switch, $datas, $info, $liveStation)
    {
        $valArray = $this->statview->incDown5($datas, $switch, $config, $info, $liveStation)[$ntab];

        $page = $this->searchHTML('down_5', 'inc');
        $page = str_replace('{CSS_DOWN}',  $valArray['CSS_DOWN'], $page);
        $page = str_replace('{TEXT_DOWN_SMALL_n}',  $valArray['TEXT_DOWN_SMALL_n'], $page);
        $page = str_replace('{ALTERN_TXT_S_1n}',  $valArray['ALTERN_TXT_S_1n'], $page);
        $page = str_replace('{_VALUE_n}',  $valArray['_VALUE_n'], $page);
        $page = str_replace('{ALTERN_TXT_S_2n}',  $valArray['ALTERN_TXT_S_2n'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_SMALLn}',  $valArray['CLASS_UNIT_DOWN_SMALLn'], $page);
        $page = str_replace('{_UNIT_DOWN_SMALLn}',  $valArray['_UNIT_DOWN_SMALLn'], $page);
        $page = str_replace('{ALTERN_TXT_S_3n}',  $valArray['ALTERN_TXT_S_3n'], $page);
        $page = str_replace('{TEXT_DOWN_LARGE_n}',  $valArray['TEXT_DOWN_LARGE_n'], $page);
        $page = str_replace('{ALTERN_TXT_L_1n}',  $valArray['ALTERN_TXT_L_1n'], $page);
        $page = str_replace('{ALTERN_TXT_L_2n}',  $valArray['ALTERN_TXT_L_2n'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_LARGEn}',  $valArray['CLASS_UNIT_DOWN_LARGEn'], $page);
        $page = str_replace('{_UNIT_DOWN_LARGEn}',  $valArray['_UNIT_DOWN_LARGEn'], $page);
        $page = str_replace('{ALTERN_TXT_L_3n}',  $valArray['ALTERN_TXT_L_3n'], $page);
        $page = str_replace('{TEXT_DOWN_SMALL_x}',  $valArray['TEXT_DOWN_SMALL_x'], $page);
        $page = str_replace('{ALTERN_TXT_S_1x}',  $valArray['ALTERN_TXT_S_1x'], $page);
        $page = str_replace('{_VALUE_x}',  $valArray['_VALUE_x'], $page);
        $page = str_replace('{ALTERN_TXT_S_2x}',  $valArray['ALTERN_TXT_S_2x'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_SMALLx}',  $valArray['CLASS_UNIT_DOWN_SMALLx'], $page);
        $page = str_replace('{_UNIT_DOWN_SMALLx}',  $valArray['_UNIT_DOWN_SMALLx'], $page);
        $page = str_replace('{ALTERN_TXT_S_3x}',  $valArray['ALTERN_TXT_S_3x'], $page);
        $page = str_replace('{TEXT_DOWN_LARGE_x}',  $valArray['TEXT_DOWN_LARGE_x'], $page);
        $page = str_replace('{ALTERN_TXT_L_1x}',  $valArray['ALTERN_TXT_L_1x'], $page);
        $page = str_replace('{ALTERN_TXT_L_2x}',  $valArray['ALTERN_TXT_L_2x'], $page);
        $page = str_replace('{CLASS_UNIT_DOWN_LARGEx}',  $valArray['CLASS_UNIT_DOWN_LARGEx'], $page);
        $page = str_replace('{_UNIT_DOWN_LARGEx}',  $valArray['_UNIT_DOWN_LARGEx'], $page);
        $page = str_replace('{ALTERN_TXT_L_3x}',  $valArray['ALTERN_TXT_L_3x'], $page);
        return $page;
    }

    //Down Unique Sun-UV
    public function modDown6($config, $tab, $datas, $info, $liveStation)
    {
        $page = $this->searchHTML('down_6', 'inc');
        $page = str_replace('{DOWN_SPECIAL}',  $this->statview->incDownSun($config, $tab, $datas, $info, $liveStation), $page);
        return $page;
    }

    //Down Unique Rain-Rate-Cloudy
    public function modDown7($config, $tab, $datas, $info, $liveStation)
    {
        $page = $this->searchHTML('down_6', 'inc');
        $page = str_replace('{DOWN_SPECIAL}',  $this->statview->incDownCloudy($config, $datas, $info, $liveStation), $page);
        return $page;
    }



    public function dmyArrow($string, $switch)
    {
        if ($string == "left") {
            $page = (($switch['s_dmy'] == 'M') || ($switch['s_dmy'] == 'Y')) ? 'dmy_arrow_left' : '';
        }
        if ($string == "right") {
            $page = (($switch['s_dmy'] == 'M') || ($switch['s_dmy'] == 'D')) ? 'dmy_arrow_right' : '';
        }
        return $page;
    }

    public function dmy1($switch)
    {
        $page = '';
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('DAILY') . '" class="dmy dmy_18 dmy_left" onclick="$(\'#inputdmy\').val(\'D\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'Y') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_18 dmy_left" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'D') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_18 dmy_right" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('YEARLY') . '" class="dmy dmy_18 dmy_right" onclick="$(\'#inputdmy\').val(\'Y\'); $(\'#formdmy\').submit()"></a>';
        }
        return $page;
    }

    public function dmy2($switch)
    {
        $page = '';
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('DAILY') . '" class="dmy dmy_18 dmy_left" onclick="$(\'#inputdmy\').val(\'D\'); $(\'#formdmy\').submit()"><div class="arrow_left"></div></a>';
        }
        if ($switch['s_dmy'] == 'Y') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_18 dmy_left" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"><div class="arrow_left"></div></a>';
        }
        if ($switch['s_dmy'] == 'D') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_18 dmy_right" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"><div class="arrow_right"></div></a>';
        }
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('YEARLY') . '" class="dmy dmy_18 dmy_right" onclick="$(\'#inputdmy\').val(\'Y\'); $(\'#formdmy\').submit()"><div class="arrow_right"></div></a>';
        }
        return $page;
    }

    public function dmy3($switch)
    {
        $page = '';
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('DAILY') . '" class="dmy dmy_9 dmy_left" onclick="$(\'#inputdmy\').val(\'D\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'Y') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_9 dmy_left" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'D') {
            $page .= '<a title="' . $this->l->trad('MONTHLY') . '" class="dmy dmy_9 dmy_right" onclick="$(\'#inputdmy\').val(\'M\'); $(\'#formdmy\').submit()"></a>';
        }
        if ($switch['s_dmy'] == 'M') {
            $page .= '<a title="' . $this->l->trad('YEARLY') . '" class="dmy dmy_9 dmy_right" onclick="$(\'#inputdmy\').val(\'Y\'); $(\'#formdmy\').submit()"></a>';
        }
        return $page;
    }

    public function dmyLetter($switch)
    {
        if ($switch['s_dmy'] == 'D') {
            $page = $this->l->trad('D');
        }
        if ($switch['s_dmy'] == 'M') {
            $page = $this->l->trad('M');
        }
        if ($switch['s_dmy'] == 'Y') {
            $page = $this->l->trad('Y');
        }

        return $page;
    }





    public function addSelect($options)
    {
        $addOptions = '';
        $i = 0;
        foreach ($options as $key => $values) {
            $addOptions .= '<option class="small1000" value="' . $key . '">' . ++$i . '- ' . $values['txt'] . '</option>';
            $addOptions .= '<option class="large1000" value="' . $key . '">' . $i . '- ' . $values['text'] . '</option>';
        }
        return $addOptions;
    }

    public function getTitle($param, $datas, $info, $liveStation)
    {
        $time = $this->statview->getAPIDatasUp($datas, $info, $liveStation)['time'];
        $station_name = $this->statview->getAPIDatas($datas, $info, $liveStation)['station_name'];

        $this->page .= $this->searchHTML('title', 'home');
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{_LOCATION}',  $station_name, $this->page);
        $this->page = str_replace('{_DATE}',  $this->statview->DateStation($time, $this->l->getLg()), $this->page);
    }

    public function getMenu($param, $switch)
    {
        $this->page .= $this->searchHTML('menu', 'home');
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
        $this->page = str_replace('{STYLES}',  $this->l->trad('STYLES'), $this->page);
        $this->page = str_replace('{DESIGN}',  $this->l->trad('DESIGN'), $this->page);
        $this->page = str_replace('{DAY_NIGHT}',  $this->l->trad('DAY_NIGHT'), $this->page);
        $this->page = str_replace('{COLOR}',  $this->l->trad('COLOR'), $this->page);
        $this->page = str_replace('{ICONS}',  $this->l->trad('ICONS'), $this->page);
        $this->page = str_replace('{UNITS}',  $this->l->trad('UNITS'), $this->page);
        $this->page = str_replace('{TEMPERATURE}',  $this->l->trad('TEMPERATURE'), $this->page);
        $this->page = str_replace('{WIND}',  $this->l->trad('WIND'), $this->page);
        $this->page = str_replace('{RAIN}',  $this->l->trad('RAIN'), $this->page);
        $this->page = str_replace('{PRESSURE}',  $this->l->trad('PRESSURE'), $this->page);
        $this->page = str_replace('{MENU}',  $this->l->trad('MENU'), $this->page);
        $this->page = str_replace('{SETTINGS}',  $this->l->trad('SETTINGS'), $this->page);
        $this->page = str_replace('{ADMIN}',  $this->l->trad('ADMIN'), $this->page);
        $this->page = str_replace('{_DRAP}',  $param['_DRAP'], $this->page);
        $this->page = str_replace('{_SOUS_MENU_CSS}',  $this->getLiMenu($param, 's_css', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_DAY_NIGHT}',  $this->getLiMenu($param, 's_daynight', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_COLOR}',  $this->getLiMenu($param, 's_color', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_ICON}',  $this->getLiMenu($param, 's_icon', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_TEMP}',  $this->getLiMenu($param, 's_temp', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_WIND}',  $this->getLiMenu($param, 's_wind', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_RAIN}',  $this->getLiMenu($param, 's_rain', $switch), $this->page);
        $this->page = str_replace('{_SOUS_MENU_PRESSURE}',  $this->getLiMenu($param, 's_press', $switch), $this->page);
    }

    public function getLiMenu($param, $var, $switch)
    {
        $page = $this->searchHTML('menuList', 'home');
        $page = str_replace('{_LG}',  $param['_LG'], $page);
        $page = str_replace('{_VAR}', $var, $page);
        $page = str_replace('{_LI_VAR}', $this->addLiMenu($var, $switch), $page);
        return $page;
    }

    public function addLiMenu($var, $switch)
    {
        switch ($var) {
            case 's_css':
                $array = array(
                    "bluedark" => $this->l->trad('DARK_BLUE'),
                    "bluelight" => $this->l->trad('LIGHT_BLUE'),
                    "black" => $this->l->trad('BLACK'),
                    "white" => $this->l->trad('WHITE')
                );
                break;
            case 's_daynight':
                $array = array(
                    'on' => $this->l->trad('ON'),
                    'off' => $this->l->trad('OFF')
                );
                break;
            case 's_color':
                $array = array(
                    "neutral" => $this->l->trad('NEUTRAL'),
                    "colored" => $this->l->trad('COLORED'),
                    "dynamic" => $this->l->trad('DYNAMIC')
                );
                break;
            case 's_icon':
                $array = array(
                    'yes' => $this->l->trad('YES'),
                    'no' => $this->l->trad('NO')
                );
                break;
            case 's_temp':
                $array = array(
                    "C" => $this->l->trad('CELSIUS'),
                    "F" => $this->l->trad('FAHRENHEIT')
                );
                break;
            case 's_wind':
                $array = array(
                    "kph" => $this->l->trad('KPH'),
                    "mph" => $this->l->trad('MPH')
                );
                break;
            case 's_rain':
                $array = array(
                    "mm" => $this->l->trad('MM'),
                    "in" => $this->l->trad('IN')
                );
                break;
            case 's_press':
                $array = array(
                    "hpa" => $this->l->trad('HPA'),
                    "inhg" => $this->l->trad('INHG')
                );
                break;
        }


        $page = '';
        foreach ($array as $key => $li) {
            $active = ($switch[$var] == $key) ? 'pushy-active' : '';
            $page .=  '<li>';
            $page .=  '<a onclick="$(\'#' . $var . '\').val(\'' . $key . '\'); $(\'#form_menu' . $var . '\').submit()" href="#" class="' . $active . '">';
            $page .=  '<i class="fas fa-caret-right"></i> &nbsp;' . $li;
            $page .=  '</a>';
            $page .=  '</li>';
        }
        return $page;
    }

    public function getBurger()
    {
        $this->page .= $this->searchHTML('burger', 'home');
        $this->page = str_replace('{MENU}',  $this->l->trad('MENU'), $this->page);
        $this->page = str_replace('{MENU_TITLE}',  $this->l->trad('MENU_TITLE'), $this->page);
    }



    public function getHeader($param)
    {
        $this->page .= $this->searchHTML('header', 'home');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
        $this->page = str_replace('{_ROOT}',  $param['_ROOT'], $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
    }

    public function getMenuList($param)
    {
        $this->page .= $this->searchHTML('menuList', 'home');
        $this->page = str_replace('{_LOGO}',  $param['_LOGO'], $this->page);
        $this->page = str_replace('{_ROOT}',  $param['_ROOT'], $this->page);
        $this->page = str_replace('{_LG}',  $param['_LG'], $this->page);
    }


    public function getCSS($switch, $datas, $info, $liveStation)
    {
        $apiDatasUP = $this->statview->getAPIDatasUp($datas, $info, $liveStation);
        $time = $apiDatasUP['time'];
        $sunset = $apiDatasUP['time_sunset'];
        $sunrise = $apiDatasUP['time_sunrise'];
        $Ttime = $this->statview->TimeStation($time);
        $Tsunrise = $this->statview->TimeStation($sunrise);
        $Tsunset = $this->statview->TimeStation($sunset);

        $css = $switch['s_css'];
        $daynight = $switch['s_daynight'];

        if ($daynight == 'on') {
            if ($css == 'bluelight' || $css == 'bluedark') {
                $result = ($Ttime > $Tsunrise && $Ttime < $Tsunset) ? 'bluelight' : 'bluedark';
            } elseif ($css == 'white' || $css == 'black') {
                $result = ($Ttime > $Tsunrise && $Ttime < $Tsunset) ? 'white' : 'black';
            }
        } elseif ($daynight == 'off') {
            $result = $css;
        }
        return $result;
    }
}
