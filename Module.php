<?php
namespace Modules\NoticeBoardModule;

use Zabbix\Core\CModule;
use APP;
use CMenuItem;
use CWebUser;
use CView;

class Module extends CModule {

    public function init(): void {
        // Guard umbrella Informativos
        if (defined('ZBX_INFORMATIVOS_ACTIVE') && ZBX_INFORMATIVOS_ACTIVE === true) { return; }
        CView::registerDirectory(__DIR__ . '/views');

        try {
            $menu = APP::Component()->get('menu.main');
        } catch (\Throwable $e) {
            return;
        }

        // Submenu em Monitoramento: visível para todos os perfis
        $menu->findOrAdd(_('Monitoring'))
            ->getSubMenu()
            ->add((new CMenuItem(_('Notice Board')))->setAction('notice_board.dashboard'));

        // Painel de gestão no menu: somente Super Admin (único que cria/edita/exclui).
        // Admin e Usuário veem o Notice Board apenas em Monitoramento.
        if (CWebUser::getType() !== USER_TYPE_SUPER_ADMIN) {
            return;
        }

        $adminItem = (new CMenuItem(_('Notice Board')))
            ->setAction('notice_board.view');

        // Super Admin: anexa dentro de "Administração" (sem ícone próprio, herda estrutura)
        foreach ($menu->getMenuItems() as $item) {
            if ($item->getLabel() === _('Administration') || $item->getLabel() === 'Administration') {
                $item->getSubMenu()->add($adminItem);
                return;
            }
        }

        // Fallback: menu "Administração" não encontrado. Item raiz precisa de
        // ícone explícito para aparecer corretamente na sidebar.
        $menu->add($adminItem->setIcon(ZBX_ICON_BELL));
    }
}
