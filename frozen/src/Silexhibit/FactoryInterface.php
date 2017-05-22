<?php

// Silexhibit Factory Interface
// ============================
// All factory classes implement this relatively simple interface.

namespace Silexhibit;

interface FactoryInterface
{
  // `getInstanceById` means all instances of an object type or class musted be
  // indexed by a unique `id`. Whether or not that id is stored separately in
  // the factory or in the instance is up to the implementation.
  public function getInstanceById($id);
}
