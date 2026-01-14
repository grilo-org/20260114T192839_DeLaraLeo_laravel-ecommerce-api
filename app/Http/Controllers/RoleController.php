<?php

namespace App\Http\Controllers;

use App\Application\UseCases\AssignPermissionToRoleUseCase;
use App\Application\UseCases\AssignRoleToUserUseCase;
use App\Application\UseCases\RemovePermissionFromRoleUseCase;
use App\Application\UseCases\RemoveRoleFromUserUseCase;
use App\Domain\Repositories\RoleRepositoryInterface;
use App\Http\Requests\AssignPermissionToRoleRequest;
use App\Http\Requests\AssignRoleToUserRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    ) {
    }

    /**
     * Get all roles.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = $this->roleRepository->getAll();
        return RoleResource::collection($roles);
    }

    public function assignToUser(AssignRoleToUserRequest $request, AssignRoleToUserUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->validated()['user_id'], $request->validated()['role_slug']);

        return response()->json([
            'message' => 'Role assigned successfully.',
        ], 200);
    }

    public function removeFromUser(AssignRoleToUserRequest $request, RemoveRoleFromUserUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->validated()['user_id'], $request->validated()['role_slug']);

        return response()->json([
            'message' => 'Role removed successfully.',
        ], 200);
    }

    public function assignPermission(AssignPermissionToRoleRequest $request, AssignPermissionToRoleUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->validated()['role_slug'], $request->validated()['permission_slug']);

        return response()->json([
            'message' => 'Permission assigned to role successfully.',
        ], 200);
    }

    public function removePermission(AssignPermissionToRoleRequest $request, RemovePermissionFromRoleUseCase $useCase): JsonResponse
    {
        $useCase->execute($request->validated()['role_slug'], $request->validated()['permission_slug']);

        return response()->json([
            'message' => 'Permission removed from role successfully.',
        ], 200);
    }
}

