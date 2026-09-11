<?php

namespace Modules\NoticeBoardModule\Actions;

use CController;
use CControllerResponseData;
use CControllerResponseRedirect;
use CUrl;

class CControllerNoticeBoardEdit extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return $this->validateInput(['id' => 'required|int32']);
    }

    // Somente Super Admin edita avisos. Admin acessa o painel apenas para leitura.
    protected function checkPermissions(): bool {
        return $this->getUserType() === USER_TYPE_SUPER_ADMIN;
    }

    protected function doAction(): void {
        $id = (int) $this->getInput('id');

        $result = DBselect('SELECT * FROM notice_board WHERE id=' . $id);
        $notice = DBfetch($result) ?: null;

        if (!$notice) {
            $this->setResponse(new CControllerResponseRedirect(
                (new CUrl('zabbix.php'))->setArgument('action', 'notice_board.view')
            ));
            return;
        }

        $groups = [];
        $result = DBselect('SELECT usrgrpid, name FROM usrgrp ORDER BY name');
        while ($row = DBfetch($result)) {
            $groups[] = $row;
        }

        $this->setResponse(new CControllerResponseData([
            'notice'         => $notice,
            'groups'         => $groups,
            'mode'           => 'edit',
            'is_super_admin' => true,
        ]));
    }
}
