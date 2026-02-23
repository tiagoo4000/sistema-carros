<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProfileForm from '@/Components/User/ProfileForm.vue';

const props = defineProps({
    usuario: Object
});

</script>

<template>
    <Head title="Editar Usuário" />

    <AdminLayout>
        <div class="max-w-5xl mx-auto">
            <div class="md:flex md:items-center md:justify-between mb-6">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-slate-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Editar Usuário: {{ usuario.nome }}
                    </h2>
                </div>
            </div>

            <ProfileForm
                mode="admin"
                :user="usuario"
                :submit-url="route('admin.usuarios.update', usuario.id)"
            />
        </div>
    </AdminLayout>
</template>
