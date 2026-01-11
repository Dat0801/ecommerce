<?php

namespace App\Interfaces;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function getAll();

    /**
     * Get record by ID
     */
    public function getById($id);

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Update a record
     */
    public function update($id, array $data);

    /**
     * Delete a record
     */
    public function delete($id);
}
