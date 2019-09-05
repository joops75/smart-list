<template>
    <div>
        <button type="button" class="btn btn-primary" @click="handleClick" data-mode="Create" data-toggle="modal" data-target="#createProjectModal">
            Create New Project
        </button>
    </div>
</template>

<script>
export default {
    props: ['handleClick']
}
</script>