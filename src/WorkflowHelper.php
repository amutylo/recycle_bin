<?php

namespace Drupal\recycle_bin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\state_machine\Plugin\Workflow\WorkflowInterface;

class WorkflowHelper {
  /**
   * Checks if a state is set as published in a certain workflow.
   *
   * @param string $state_id
   *   The ID of the state to check.
   * @param \Drupal\state_machine\Plugin\Workflow\WorkflowInterface $workflow
   *   The workflow the state belongs to.
   *
   * @return bool|null
   *   TRUE|FALSE if the state is set in the workflow, NULL otherwise.
   *
   * @throwns \InvalidArgumentException
   *   Thrown when the workflow is not plugin based, because this is required to
   *   retrieve the publication state from the workflow states.
   */
  public function isWorkflowStatePublished($state_id, WorkflowInterface $workflow) {
    // We rely on being able to inspect the plugin definition. Throw an error if
    // this is not the case.
    if (!$workflow instanceof PluginInspectionInterface) {
      $label = $workflow->getLabel();
      throw new \InvalidArgumentException("The '$label' workflow is not plugin based.");
    }
    // Retrieve the raw plugin definition, as all additional plugin settings
    // are stored there.
    $raw_workflow_definition = $workflow->getPluginDefinition();
    $isPublishedState = $raw_workflow_definition['states'][$state_id]['published'] ?? NULL;
    return $isPublishedState;
  }
}
