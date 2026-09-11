<?php

namespace Modules\NoticeBoardModule\Actions;

use CController;
use CControllerResponseRedirect;
use CUrl;

class CControllerNoticeBoardDelete extends CController {

    protected function init(): void {
        $this->disableCsrfValidation();
    }

    protected function checkInput(): bool {
        return $this->validateInput(['id' => 'required|int32']);
    }

    // Somente Super Admin exclui avisos. Admin acessa o painel apenas para leitura.
    protected function checkPermissions(): bool {
        return $this->getUserType() === USER_TYPE_SUPER_ADMIN;
    }

    protected function doAction(): void {
        $id = (int) $this->getInput('id');

        DBexecute('DELETE FROM notice_board WHERE id=' . $id);

        $this->setResponse(new CControllerResponseRedirect(
            (new CUrl('zabbix.php'))->setArgument('action', 'notice_board.view')
        ));
    }
}
