<?php

namespace App\Controller\Admin;

use App\Constant\FileConstant;
use App\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

class FileCrudController extends AbstractCrudController
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/'.FileConstant::FILE_UPLOAD_DIR)]
        private readonly string $uploadDir
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $uploadField = Field::new('uploadedFile')
                ->setFormType(FileUploadType::class)
                ->setFormTypeOptions(['upload_dir' => $this->uploadDir, 'mapped' => false])
                ->setRequired(Crud::PAGE_NEW === $pageName)
                ->setHelp('Supported types: '.implode(', ', array_keys(FileConstant::FILE_MIME_TYPES_BY_EXTENSION)))
                ->onlyOnForms();

        return [
            TextField::new('name')->setRequired(true),
            TextField::new('slug')->setRequired(true),
            $uploadField,
            TextField::new('mimeType')->onlyOnIndex()->setSortable(false),
        ];
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        return $this->addFileEventListener($formBuilder);
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        return $this->addFileEventListener($formBuilder);
    }

    private function addFileEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::SUBMIT, $this->handleFileUpload());
    }

    private function handleFileUpload(): callable
    {
        return function (FormEvent $event) {
            $form = $event->getForm();
            if (null === $form->get('uploadedFile')->getData()) {
                return;
            }

            /** @var UploadedFile $file */
            $file = $form->get('uploadedFile')->getNormData();
            $form->remove('uploadedFile');
            $data = $form->getData();
            Assert::object($data);
            Assert::isAOf($data, File::class);

            if (!in_array($file->getMimeType(), FileConstant::FILE_MIME_TYPES, true)) {
                $form->addError(new FormError('The given file type is not supported!'));
            }

            /* @var File $data */
            $data->setMimeType($file->getMimeType());
            $data->setSize($file->getSize());
            $data->setFile($file->getContent());
        };
    }
}
