<?php

namespace Modules\NoticeBoardModule\Actions;

use CController;
use CControllerResponseData;

class CControllerNoticeBoardCreate extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return true;
    }

    // Somente Super Admin cria avisos. Admin acessa o painel apenas para leitura.
    protected function checkPermissions(): bool {
        return $this->getUserType() === USER_TYPE_SUPER_ADMIN;
    }

    protected function doAction(): void {
        $notice = [
            'id'         => 0,
            'titulo'     => '',
            'conteudo'   => '',
            'tipo_borda' => 'info',
            'usrgrpid'   => 0,
            'inicio'     => date('Y-m-d H:i:s'),
            'fim'        => date('Y-m-d H:i:s', strtotime('+7 days')),
        ];

        $groups = [];
        $result = DBselect('SELECT usrgrpid, name FROM usrgrp ORDER BY name');
        while ($row = DBfetch($result)) {
            $groups[] = $row;
        }

        $this->setResponse(new CControllerResponseData([
            'notice'         => $notice,
            'groups'         => $groups,
            'mode'           => 'create',
            'is_super_admin' => true,
        ]));
    }
}
