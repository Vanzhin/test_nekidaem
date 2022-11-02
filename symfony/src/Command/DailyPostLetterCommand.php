<?php

namespace App\Command;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;
use App\Service\Mailer;

#[AsCommand(
    name: 'app:daily-postletter',
    description: 'Daily mail subscription',
)]
class DailyPostLetterCommand extends Command
{
    private PostRepository $postRepository;
    private PaginatorInterface $paginator;
    private UserRepository $userRepository;
    private Mailer $mailer;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator, UserRepository $userRepository, Mailer $mailer)
    {
        parent::__construct();

        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();
        $io = new SymfonyStyle($input, $output);
        $io->progressStart(count($users));
        foreach ($users as $user) {
            $io->progressAdvance();
            $posts = $this->postRepository->findPostsQuery($user);
            $pagination = $this->paginator->paginate(
                $posts, /* query NOT result */
                1/*page number*/,
                5/*limit per page*/
            );
            if ($pagination->count() > 0){
                $this->mailer->sendDailyMail( $user, $pagination);
            }

        }
        $io->progressFinish();
    }
}
