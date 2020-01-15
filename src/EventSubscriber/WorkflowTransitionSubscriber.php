<?php

namespace Drupal\recycle_bin\EventSubscriber;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\recycle_bin\WorkflowHelper;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;

class WorkflowTransitionSubscriber implements EventSubscriberInterface {

  /**
   * The workflow helper.
   *
   * @var \Drupal\recycle_bin\WorkflowHelper
   */
  protected $workflowHelper;

  public function __construct(WorkflowHelper $workflowHelper) {
    $this->workflowHelper = $workflowHelper;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    $events = [
      'recycle_bin.pre_transition' => 'handleAction',
      'recycle_bin.recycled.pre_transition' => 'handleAction',
    ];
    return $events;
  }

  public function handleAction(WorkflowTransitionEvent $event) {
    $entity = $event->getEntity();

    // Verify if the new state is marked as published state.
    $is_published_state = $this->isPublishedState($event->getToState(), $event->getWorkflow());

    if ($entity instanceof EntityPublishedInterface) {
      if ($is_published_state) {
        $entity->setPublished();
      }
      else {
        $entity->setUnpublished();
      }
    }
  }

  /**
   * Checks if a state is set as published in a certain workflow.
   *
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowState $state
   *   The state to check.
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowInterface $workflow
   *   The workflow the state belongs to.
   *
   * @return bool
   *   TRUE if the state is set as published in the workflow, FALSE otherwise.
   */
  protected function isPublishedState(WorkflowState $state, WorkflowInterface $workflow) {
    return $this->workflowHelper->isWorkflowStatePublished($state->getId(), $workflow);
  }
}
