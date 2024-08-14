<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { onMounted, reactive, ref, computed } from "vue";
import InputError from "@/Components/InputError.vue";
import { getToday } from "@/common";
import MicroModal from "@/Components/MicroModal.vue";

interface Validation {
  customer_id: string;
}

interface Props {
  items: Array<Item>;
  errors: Validation;
}

const props = defineProps<Props>();

interface ItemList {
  id: number;
  name: string;
  price: number;
  quantity: number;
}

const itemList = ref<Array<ItemList>>();
onMounted(() => {
  form.date = getToday();
  itemList.value = props.items.map((item) => {
    return { ...item, quantity: 0 };
  });
});

const quantity: Array<number> = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

const form = reactive<{
  date: string | null;
  customer_id: number | null;
  status: boolean;
  items: Array<{ id: number; quantity: number }>;
}>({
  date: null,
  customer_id: null,
  status: true,
  items: [],
});

const tatalPrice = computed(() => {
  return itemList.value?.reduce((accumulator, currentItem) => {
    return (accumulator += currentItem.price * currentItem.quantity);
  }, 0);
});

const storePurchase = () => {
  itemList.value?.forEach((item) => {
    console.log(item);
    if (item.quantity === 0) return;

    form.items.push({
      id: item.id,
      quantity: item.quantity,
    });
  });
  router.post("/purchases", form);
};

const setCustomerId = (id: number) => {
  form.customer_id = id;
};
</script>

<template>
  <Head title="購入画面" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">購入画面</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <section class="text-gray-600 body-font relative">
            <form @submit.prevent="storePurchase">
              <div class="container px-5 py-8 mx-auto">
                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                  <div class="flex flex-wrap -m-2">
                    <div class="p-2 w-full">
                      <div class="relative">
                        <label for="date" class="leading-7 text-sm text-gray-600"
                          >日付</label
                        >
                        <input
                          type="date"
                          id="date"
                          name="date"
                          v-model="form.date"
                          class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        />
                      </div>
                    </div>

                    <div class="p-2 w-full">
                      <div class="relative">
                        <label for="customer" class="leading-7 text-sm text-gray-600"
                          >会員名</label
                        >
                        <MicroModal @update:customerId="setCustomerId" />
                        <InputError :message="errors.customer_id" />
                      </div>
                    </div>

                    <div class="p-2 w-full">
                      <div>
                        <label for="date" class="leading-7 text-sm text-gray-600"
                          >日付</label
                        >
                        <input
                          type="text"
                          id="date"
                          name="date"
                          v-model="form.date"
                          class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        />
                      </div>
                    </div>

                    <div class="p-2 w-full">
                      <table class="table-auto w-full text-left whitespace-no-wrap">
                        <thead>
                          <tr>
                            <th
                              class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl"
                            >
                              Id
                            </th>
                            <th
                              class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                            >
                              商品名
                            </th>
                            <th
                              class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                            >
                              価格
                            </th>
                            <th
                              class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                            >
                              数量
                            </th>
                            <th
                              class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100"
                            >
                              小計
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="item in itemList" :key="item.id">
                            <td class="px-4 py-3 border-b-2 border-gray-200">
                              {{ item.id }}
                            </td>
                            <td class="px-4 py-3 border-b-2 border-gray-200">
                              {{ item.name }}
                            </td>
                            <td class="px-4 py-3 border-b-2 border-gray-200">
                              {{ item.price }}
                            </td>
                            <td class="px-4 py-3 border-b-2 border-gray-200">
                              <select name="quantity" v-model="item.quantity" id="">
                                <option v-for="q in quantity" :value="q">{{ q }}</option>
                              </select>
                            </td>
                            <td class="px-4 py-3 border-b-2 border-gray-200">
                              {{ item.price * item.quantity }}
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <div class="p-2 w-full">
                      <div>
                        <label for="price" class="leading-7 text-sm text-gray-600"
                          >合計金額</label
                        >
                        <div
                          class="lg:w-1/2 md:w-2/3 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                        >
                          {{ tatalPrice }}円
                        </div>
                      </div>
                    </div>
                    <div class="p-2 w-full">
                      <button
                        class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg"
                      >
                        登録する
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
