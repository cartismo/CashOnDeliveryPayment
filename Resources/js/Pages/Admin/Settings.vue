<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StoreSettingsTabs from '@/Components/Admin/StoreSettingsTabs.vue';
import {
    BanknotesIcon,
    ArrowLeftIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    CurrencyDollarIcon,
    Cog6ToothIcon,
    InformationCircleIcon,
    DocumentTextIcon,
    ReceiptPercentIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    module: Object,
    stores: Array,
    storeSettings: Object,
    defaultSettings: Object,
});

const storeTabsRef = ref(null);
const saving = ref(false);

const submit = () => {
    if (!storeTabsRef.value) return;
    saving.value = true;
    router.put(route('admin.payment.cod.settings.update'), {
        store_id: storeTabsRef.value.activeStoreId,
        is_enabled: storeTabsRef.value.isEnabled,
        settings: storeTabsRef.value.localSettings,
    }, {
        preserveScroll: true,
        onFinish: () => saving.value = false,
    });
};

const resetAll = () => {
    if (confirm('Reset all settings to defaults?') && storeTabsRef.value) {
        Object.assign(storeTabsRef.value.localSettings, props.defaultSettings);
    }
};

const hasChanges = computed(() => {
    if (!storeTabsRef.value) return false;
    const currentStoreSettings = props.storeSettings[storeTabsRef.value.activeStoreId];
    if (!currentStoreSettings) return true;
    const original = { ...props.defaultSettings, ...(currentStoreSettings.settings || {}) };
    return JSON.stringify(storeTabsRef.value.localSettings) !== JSON.stringify(original) ||
           storeTabsRef.value.isEnabled !== currentStoreSettings.is_enabled;
});

const feeTypes = [
    { value: 'none', label: 'No Fee', description: 'No additional charge' },
    { value: 'fixed', label: 'Fixed Amount', description: 'Flat fee per order' },
    { value: 'percentage', label: 'Percentage', description: '% of order total' },
];

const feeDisplay = computed(() => {
    if (!storeTabsRef.value) return 'No fee';
    const settings = storeTabsRef.value.localSettings;
    const feeType = settings.fee_type;
    const amount = settings.fee_amount || 0;
    if (feeType === 'none' || amount === 0) return 'No fee';
    if (feeType === 'fixed') return `$${amount.toFixed(2)}`;
    if (feeType === 'percentage') return `${amount}%`;
    return 'No fee';
});

const hasRestrictions = computed(() => {
    if (!storeTabsRef.value) return false;
    const s = storeTabsRef.value.localSettings;
    return s.minimum_order_amount || s.maximum_order_amount;
});
</script>

<template>
    <AdminLayout :title="`${module.name} Settings`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('admin.modules.installed.index')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-yellow-500 to-amber-600 rounded-xl shadow-lg">
                            <BanknotesIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ module.name }}</h1>
                            <p class="text-sm text-gray-500">Payment Method Configuration</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span v-if="hasChanges" class="text-sm text-amber-600 font-medium">Unsaved changes</span>
                    <button type="button" @click="resetAll" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        <ArrowPathIcon class="w-4 h-4 inline mr-2" />Reset
                    </button>
                    <button type="button" @click="submit" :disabled="saving || !hasChanges" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl hover:from-yellow-600 hover:to-amber-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-amber-500/25">
                        <CheckIcon class="w-4 h-4 inline mr-2" />{{ saving ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </template>

        <StoreSettingsTabs ref="storeTabsRef" :stores="stores" :store-settings="storeSettings" :default-settings="defaultSettings" module-slug="cash-on-delivery-payment">
            <template #default="{ store, settings, updateSetting, isEnabled }">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Left Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 border-b border-gray-100"><h3 class="font-semibold text-gray-900">Module Status</h3></div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span :class="settings.enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 text-xs font-semibold rounded-full">{{ settings.enabled ? 'Active' : 'Inactive' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Version</span>
                                    <span class="text-sm font-mono text-gray-900">v{{ module.installed_version }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Type</span>
                                    <span class="text-sm text-gray-900">Payment</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-500 to-amber-600 rounded-2xl shadow-lg p-5 text-white">
                            <div class="flex items-center space-x-3 mb-3">
                                <ReceiptPercentIcon class="w-8 h-8 opacity-80" />
                                <div>
                                    <p class="text-sm opacity-80">COD Fee</p>
                                    <p class="text-xl font-bold">{{ feeDisplay }}</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-white/20 space-y-1">
                                <div v-if="settings.minimum_order_amount" class="flex items-center space-x-2">
                                    <span class="text-sm opacity-80">Min: ${{ settings.minimum_order_amount }}</span>
                                </div>
                                <div v-if="settings.maximum_order_amount" class="flex items-center space-x-2">
                                    <span class="text-sm opacity-80">Max: ${{ settings.maximum_order_amount }}</span>
                                </div>
                                <div v-if="!hasRestrictions" class="text-sm opacity-80">No order restrictions</div>
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100">
                            <div class="flex items-start space-x-3">
                                <InformationCircleIcon class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" />
                                <div>
                                    <h4 class="text-sm font-medium text-amber-900">Cash on Delivery</h4>
                                    <p class="text-sm text-amber-700 mt-1">Customers pay in cash when their order is delivered. You can add a fee to cover collection costs.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Enable Toggle -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div :class="settings.enabled ? 'bg-amber-100' : 'bg-gray-100'" class="p-3 rounded-xl transition-colors">
                                        <component :is="settings.enabled ? CheckCircleIcon : XCircleIcon" :class="settings.enabled ? 'text-amber-600' : 'text-gray-400'" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Enable Cash on Delivery</h3>
                                        <p class="text-sm text-gray-500">Accept cash payments upon delivery for {{ store?.name }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="updateSetting('enabled', !settings.enabled)" :class="settings.enabled ? 'bg-amber-500' : 'bg-gray-300'" class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                    <span :class="settings.enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out" />
                                </button>
                            </div>
                        </div>

                        <!-- General Settings -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <Cog6ToothIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">Display Settings</h2>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Display Title <span class="text-red-500">*</span></label>
                                        <input type="text" :value="settings.title" @input="updateSetting('title', $event.target.value)" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" placeholder="e.g., Cash on Delivery" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                        <input type="number" :value="settings.sort_order" @input="updateSetting('sort_order', parseInt($event.target.value) || 0)" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors" placeholder="0" />
                                        <p class="mt-1.5 text-xs text-gray-500">Lower numbers appear first</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Checkout Description</label>
                                    <textarea :value="settings.description" @input="updateSetting('description', $event.target.value)" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors resize-none" placeholder="Short description shown at checkout..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Settings -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <ReceiptPercentIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">COD Fee</h2>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Fee Type</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <label v-for="type in feeTypes" :key="type.value" :class="settings.fee_type === type.value ? 'border-amber-500 bg-amber-50 ring-2 ring-amber-500' : 'border-gray-200 hover:border-gray-300'" class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all">
                                            <input type="radio" :checked="settings.fee_type === type.value" @change="updateSetting('fee_type', type.value)" class="sr-only" />
                                            <div>
                                                <span class="block text-sm font-semibold text-gray-900">{{ type.label }}</span>
                                                <span class="mt-1 block text-xs text-gray-500">{{ type.description }}</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div v-if="settings.fee_type !== 'none'" class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ settings.fee_type === 'fixed' ? 'Fee Amount' : 'Fee Percentage' }}</label>
                                    <div class="relative w-48">
                                        <span v-if="settings.fee_type === 'fixed'" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                        <input type="number" :value="settings.fee_amount" @input="updateSetting('fee_amount', parseFloat($event.target.value) || 0)" step="0.01" min="0" :class="settings.fee_type === 'fixed' ? 'pl-10' : 'pl-4'" class="w-full pr-10 py-3 text-lg font-semibold border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors bg-white" placeholder="0" />
                                        <span v-if="settings.fee_type === 'percentage'" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">%</span>
                                    </div>
                                    <p class="mt-2 text-xs text-amber-700">{{ settings.fee_type === 'fixed' ? 'Fixed fee added to all COD orders' : 'Percentage of order total' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Restrictions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <CurrencyDollarIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">Order Restrictions</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-xl p-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Minimum Order Amount</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                            <input type="number" :value="settings.minimum_order_amount" @input="updateSetting('minimum_order_amount', parseFloat($event.target.value) || null)" step="0.01" min="0" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors bg-white" placeholder="No minimum" />
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Leave empty for no minimum</p>
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Maximum Order Amount</label>
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                            <input type="number" :value="settings.maximum_order_amount" @input="updateSetting('maximum_order_amount', parseFloat($event.target.value) || null)" step="0.01" min="0" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors bg-white" placeholder="No maximum" />
                                        </div>
                                        <p class="mt-2 text-xs text-gray-500">Leave empty for no maximum</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <DocumentTextIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">Customer Instructions</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructions for Customer</label>
                                    <textarea :value="settings.instructions" @input="updateSetting('instructions', $event.target.value)" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors resize-none" placeholder="These instructions will be shown to the customer after they place an order..."></textarea>
                                    <p class="mt-2 text-xs text-gray-500">Shown on order confirmation page and in confirmation email.</p>
                                </div>
                                <div v-if="settings.instructions" class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                        <p class="text-sm text-amber-800 whitespace-pre-wrap">{{ settings.instructions }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </StoreSettingsTabs>
    </AdminLayout>
</template>