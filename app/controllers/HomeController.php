<?php
class HomeController extends Controller
{
    public function index(array $p = []): void
    {
        $filters = [
            'busca'      => trim($_GET['busca']      ?? ''),
            'area'       => trim($_GET['area']       ?? ''),
            'status'     => trim($_GET['status']     ?? ''),
            'tecnologia' => trim($_GET['tecnologia'] ?? ''),
            'ordem'      => trim($_GET['ordem']      ?? ''),
        ];

        $perPage = 20;
        $page    = max(1, (int) ($_GET['pagina'] ?? 1));
        $total   = Projeto::count($filters);
        $projetos = Projeto::findAll($filters, $page, $perPage);
        $pagination = paginate($total, $perPage, $page);

        $this->view('home.index', [
            'pageTitle'  => 'Projetos Acadêmicos — ' . SITE_NAME,
            'projetos'   => $projetos,
            'pagination' => $pagination,
            'filters'    => $filters,
            'areas'      => Projeto::getAllAreas(),
            'tecnologias'=> Projeto::getAllTecnologias(),
            'flash'      => $this->getFlash(),
            'usuario'    => $this->currentUser(),
        ]);
    }
}
