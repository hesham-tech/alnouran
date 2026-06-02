<template>
  <div class="apph">
    <h1>إدارة المتاجر</h1>

    <!-- إضافة متجر جديد -->
    <div class="btns-grouph">
      <V-row>
        <v-col cols="4">
          <v-text-field
            density="compact"
            type="text"
            label="اسم المتجر"
            v-model="newStoreName"
          ></v-text-field
        ></v-col>
        <v-col cols="4">
          <v-btn color="dengir" height="45px" padding="4px 8px" margin="4px 0" @click="addStore"
            >إضافة متجر</v-btn
          >
        </v-col>
      </V-row>
    </div>

    <div v-for="(store, storeIndex) in stores" :key="storeIndex" class="storeh">
      <v-row class="btns-grouph">
        <v-col cols="4">
          <h2>{{ store.storeName }}</h2>
        </v-col>
        <v-col cols="4">
          <v-btn
            color="danger"
            height="45px"
            padding="4px 8px"
            margin="4px 0"
            @click="removeStore(storeIndex)"
            >حذف المتجر</v-btn
          >
        </v-col>
      </v-row>
      <!-- إضافة صنف جديد داخل المتجر -->
      <v-row class="btns-grouph">
        <v-col cols="4">
          <v-text-field
            density="compact"
            type="text"
            label="اسم الصنف"
            v-model="newCategoryName"
          ></v-text-field
        ></v-col>
        <v-col cols="4">
          <v-btn
            color="primary"
            height="45px"
            padding="4px 8px"
            margin="4px 0"
            @click="addCategory(storeIndex)"
            >إضافة صنف</v-btn
          >
        </v-col>
      </v-row>
      <div
        v-for="(category, categoryIndex) in store.categories"
        :key="categoryIndex"
        class="category"
      >
        <v-row class="btns-grouph">
          <v-col cols="4">
            <h2>{{ category.categoryName }}</h2>
          </v-col>
          <v-col cols="4">
            <v-btn
              color="danger"
              height="45px"
              padding="4px 8px"
              margin="4px 0"
              @click="removeStore(storeIndex)"
              >حذف الصنف</v-btn
            >
          </v-col>
        </v-row>

        <!-- إضافة مشتري جديد داخل الصنف -->
        <v-row class="btns-grouph">
          <v-col cols="4">
            <v-text-field
              density="compact"
              type="text"
              label="الاسم"
              v-model="newCustomerName"
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              density="compact"
              type="text"
              label="المبلغ"
              v-model="newCustomerAmount"
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              density="compact"
              type="text"
              label="الوصف"
              v-model="newCustomerDescription"
            ></v-text-field>
          </v-col>
          <v-col cols="12">
            <v-btn
              color="primary"
              height="45px"
              padding="4px 8px"
              margin="4px 0"
              @click="addCustomer(storeIndex, categoryIndex)"
              >إضافة صنف</v-btn
            >
            <!-- <v-btn @click="addCustomer(storeIndex, categoryIndex)"
             color="green" icon="mdi-plus"> </v-btn> -->
          </v-col>
        </v-row>

        <ul>
          <li v-for="(customer, customerIndex) in category.customers" :key="customerIndex">
            <span>{{ customer.name }}: {{ customer.amount }} - {{ customer.description }}</span>
            <button @click="removeCustomer(storeIndex, categoryIndex, customerIndex)">حذف</button>
            <button @click="editCustomer(storeIndex, categoryIndex, customerIndex)">تعديل</button>
          </li>
        </ul>
      </div>
    </div>

    <!-- عرض الإحصائيات -->
    <div class="statisticsh">
      <h2>الإحصائيات</h2>
      <p>إجمالي عدد الأصناف لكل متجر:</p>
      <ul>
        <li v-for="(store, storeIndex) in stores" :key="storeIndex">
          {{ store.storeName }}: {{ store.categories.length }}
        </li>
      </ul>
      <p>إجمالي المبالغ لكل متجر:</p>
      <ul>
        <li v-for="(store, storeIndex) in stores" :key="storeIndex">
          {{ store.storeName }}: {{ totalAmount(storeIndex) }}
        </li>
      </ul>
      <p>إجمالي المبالغ لكل الأصناف في كل المتاجر:</p>
      <p>{{ totalAmountAllCategories }}</p>
    </div>
  </div>
</template>

<script setup>

[
  {
    "storeName": "متجر كاجو",
    "categories": [
      {
        "categoryName": "لب",
        "customers": [
          {
            "name": "هشام محمد",
            "amount": "5",
            "description": "وصف لحالة الدفع أو السبب"
          },
          {
            "name": "السيد احمد",
            "amount": "10",
            "description": "وصف آخر لحالة الدفع أو السبب"
          }
        ]
      },
      {
        "categoryName": "فستق",
        "customers": [
          {
            "name": "علي حسن",
            "amount": "15",
            "description": "وصف لحالة الدفع أو السبب"
          },
          {
            "name": "محمود عادل",
            "amount": "20",
            "description": "وصف آخر لحالة الدفع أو السبب"
          }
        ]
      }
    ]
  },
  {
    "storeName": "متجر لوز",
    "categories": [
      {
        "categoryName": "لوز",
        "customers": [
          {
            "name": "أحمد علي",
            "amount": "30",
            "description": "وصف لحالة الدفع أو السبب"
          },
          {
            "name": "خالد محمد",
            "amount": "25",
            "description": "وصف آخر لحالة الدفع أو السبب"
          }
        ]
      },
      {
        "categoryName": "عين الجمل",
        "customers": [
          {
            "name": "يوسف حسن",
            "amount": "50",
            "description": "وصف لحالة الدفع أو السبب"
          },
          {
            "name": "سامي عبدالله",
            "amount": "45",
            "description": "وصف آخر لحالة الدفع أو السبب"
          }
        ]
      }
    ]
  }
]




import { ref, reactive } from 'vue';

const stores = ref(JSON.parse(localStorage.getItem('stores')) || []);
const newStoreName = ref('');
const newCategoryName = ref('');
const newCustomerName = ref('');
const newCustomerAmount = ref('');
const newCustomerDescription = ref('');

function addStore() {
  if (newStoreName.value.trim() !== '') {
    stores.value.push({
      storeName: newStoreName.value,
      categories: [],
    });
    newStoreName.value = '';
    updateLocalStorage();
  }
}

function addCategory(storeIndex) {
  if (newCategoryName.value.trim() !== '') {
    stores.value[storeIndex].categories.push({
      categoryName: newCategoryName.value,
      customers: [],
    });
    newCategoryName.value = '';
    updateLocalStorage();
  }
}

function addCustomer(storeIndex, categoryIndex) {
  if (newCustomerName.value.trim() !== '' && newCustomerAmount.value.trim() !== '') {
    stores.value[storeIndex].categories[categoryIndex].customers.push({
      name: newCustomerName.value,
      amount: newCustomerAmount.value,
      description: newCustomerDescription.value,
    });
    newCustomerName.value = '';
    newCustomerAmount.value = '';
    newCustomerDescription.value = '';
    updateLocalStorage();
  }
}

function removeStore(storeIndex) {
  stores.value.splice(storeIndex, 1);
  updateLocalStorage();
}

function removeCategory(storeIndex, categoryIndex) {
  stores.value[storeIndex].categories.splice(categoryIndex, 1);
  updateLocalStorage();
}

function removeCustomer(storeIndex, categoryIndex, customerIndex) {
  stores.value[storeIndex].categories[categoryIndex].customers.splice(customerIndex, 1);
  updateLocalStorage();
}

function editCustomer(storeIndex, categoryIndex, customerIndex) {
  const customer = stores.value[storeIndex].categories[categoryIndex].customers[customerIndex];
  const newName = prompt('تعديل اسم المشتري:', customer.name);
  const newAmount = prompt('تعديل المبلغ:', customer.amount);
  const newDescription = prompt('تعديل الوصف:', customer.description);

  if (newName !== null) customer.name = newName;
  if (newAmount !== null) customer.amount = newAmount;
  if (newDescription !== null) customer.description = newDescription;

  updateLocalStorage();
}

function totalAmount(storeIndex) {
  return stores.value[storeIndex].categories.reduce((sum, category) => {
    return (
      sum +
      category.customers.reduce((catSum, customer) => {
        return catSum + Number(customer.amount);
      }, 0)
    );
  }, 0);
}

const totalAmountAllCategories = ref(0);

function updateTotalAmountAllCategories() {
  let total = 0;
  stores.value.forEach(store => {
    store.categories.forEach(category => {
      category.customers.forEach(customer => {
        total += Number(customer.amount);
      });
    });
  });
  totalAmountAllCategories.value = total;
}

updateTotalAmountAllCategories();

function updateLocalStorage() {
  localStorage.setItem('stores', JSON.stringify(stores.value));
}
</script>

<style scoped>
.storeh {
  background-color: #fcfcfa;
}

*{
  margin: 0px 0px;
  padding: 2px;
}
/* .apph {
}

.btns-grouph {

}

.btns-grouph input {
}



.categoryh {

}

button {

}

button:hover {
  
}

.statisticsh {

} */
</style>
