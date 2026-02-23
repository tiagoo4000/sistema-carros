<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Lock, Mail, AlertTriangle } from 'lucide-vue-next';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <Link :href="route('home')" class="flex justify-center text-3xl font-bold text-emerald-600 tracking-wider">
                LEILÃO PRO
            </Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Entre na sua conta
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ou
                <Link :href="route('register')" class="font-medium text-emerald-600 hover:text-emerald-500">
                    crie uma nova conta
                </Link>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div v-if="form.errors.email" class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <AlertTriangle class="h-5 w-5 text-red-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ form.errors.email }}</p>
                        </div>
                    </div>
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail class="h-5 w-5 text-gray-400" />
                            </div>
                            <input id="email" 
                                   v-model="form.email" 
                                   name="email" 
                                   type="email" 
                                   autocomplete="email" 
                                   required 
                                   class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" 
                                   placeholder="seu@email.com" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock class="h-5 w-5 text-gray-400" />
                            </div>
                            <input id="password" 
                                   v-model="form.password" 
                                   name="password" 
                                   type="password" 
                                   autocomplete="current-password" 
                                   required 
                                   class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" 
                                   placeholder="••••••••" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" 
                                   v-model="form.remember" 
                                   name="remember-me" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded" />
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                Lembrar de mim
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">
                                Esqueceu a senha?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                :disabled="form.processing"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 transition-colors">
                            Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
