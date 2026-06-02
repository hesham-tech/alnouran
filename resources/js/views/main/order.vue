<template>
  <v-app>
    <v-container>
      <v-row>
        <v-col cols="12" sm="6">
          <!-- إضافة صنف جديد -->
          <v-card class="pa-3">
            <h2>إضافة بند</h2>
            <div class="d-f-wor">
              <v-text-field v-model="newCategoryName" label="اسم الصنف"></v-text-field>
              <v-btn color="primary" @click="addCategory">إضافة صنف</v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <v-expansion-panels>
          <v-expansion-panel
            v-for="(category, categoryIndex) in categories"
            :key="categoryIndex"
            cols="12"
          >
            <v-expansion-panel-title>{{ category.categoryName }}</v-expansion-panel-title>
            <!-- <div class="d-f-wor"></div> -->
            <v-expansion-panel-text>
              <v-btn color="error" @click="removeCategory(categoryIndex)">حذف الصنف</v-btn>
              <v-card class="mt-3 pa-">
                <!-- إضافة مشتري جديد داخل الصنف -->
                <v-card class="mt- pa-">
                  <h3>إضافة صنف</h3>
                  <v-row class="d-f-wor2">
                    <v-col class="">
                      <v-text-field
                        @focus="handleFocus"
                        @blur="handleBlur"
                        density="compact"
                        v-model="newCustomerName"
                        label="اسم المشتري"
                      ></v-text-field>
                    </v-col>
                    <v-col class="">
                      <v-text-field
                        @focus="handleFocus"
                        @blur="handleBlur"
                        type="number"
                        density="compact"
                        v-model="newCustomerAmount"
                        label="السعر"
                      ></v-text-field>
                    </v-col>
                    <v-col class="">
                      <v-text-field
                        @focus="handleFocus"
                        @blur="handleBlur"
                        density="compact"
                        v-model="newCustomerDescription"
                        label="الوصف"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                  <v-btn color="primary" @click="addCustomer(categoryIndex)">إضافة مشتري</v-btn>
                </v-card>
                <div v-if="category.customers.length > 0">
                  <v-table>
                    <thead>
                      <tr>
                        <th class="text-left">الاسم</th>
                        <th class="text-left">السعر</th>
                        <th class="text-left">الوصف</th>
                        <th class="text-left">#</th>
                        <!-- <th class="text-left">#</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(customer, customerIndex) in category.customers"
                        :key="customerIndex"
                      >
                        <td>{{ customer.name }}</td>
                        <td>{{ customer.amount }}</td>
                        <td>{{ customer.description }}</td>
                        <td>
                          <v-btn color="error" @click="removeCustomer(categoryIndex, customerIndex)"
                            >x</v-btn
                          >
                        </td>
                        <!-- <td>
                      <v-btn color="primary" @click="editCustomer(categoryIndex, customerIndex)"
                        >تعديل</v-btn
                      >
                    </td> -->
                      </tr>
                    </tbody>
                  </v-table>
                </div>
                <p v-else>لا توجد بيانات</p>
              </v-card>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
      </v-row>

      <v-row>
        <v-card class="mt-3 w-100 px-5" v-if="categories.length > 0">
          <div id="textShare">
            <div v-for="(category, categoryIndex) in categories" :key="categoryIndex">
              <h2>{{ category.categoryName }}</h2>
              <ul
                class="ul-display"
                v-for="(customer, customerIndex) in category.customers"
                :key="customerIndex"
              >
                <!-- عرض البيانات -->
                <li>
                  <span>{{ customer.amount }} </span> <span> {{ customer.description }} </span>
                </li>
              </ul>
            </div>
            <p>المبلغ الاجمالي : {{ totalAmount }}</p>
            <p>إجمالي عدد الأصناف: {{ totalCategories }}</p>
          </div>
          <v-btn color="success" @click="share()">مشاركة</v-btn>
        </v-card>
        <p v-else>لا توجد بيانات</p>
      </v-row>

      <!-- عرض الإحصائيات -->
      <!-- <v-row>
        <v-col cols="12">
          <v-card class="mt-3 pa-3">
            <h2>الإحصائيات</h2>
          </v-card>
        </v-col>
      </v-row> -->
    </v-container>
  </v-app>
</template>

<script setup>
import { ref, computed } from 'vue';

const categories = ref(JSON.parse(localStorage.getItem('categories')) || []);
const newCategoryName = ref('');
const newCustomerName = ref('');
const newCustomerAmount = ref('');
const newCustomerDescription = ref('');

function share() {
  const textShare = document.getElementById('textShare').innerText;
  const obComment = {
    text: textShare + '\n',
  };
  navigator.share(obComment);
}

function handleFocus(event) {
  const parentDiv = event.target.closest('.d-f-wor2');
  const allParents = document.querySelectorAll('.d-f-wor2');

  allParents.forEach(div => {
    if (div !== parentDiv) {
      div.style.visibility = 'hidden';
    }
  });
}

function handleBlur() {
  const allParents = document.querySelectorAll('.d-f-wor2');
  allParents.forEach(div => {
    div.style.visibility = 'visible';
  });
}

function addCategory() {
  if (newCategoryName.value.trim() !== '') {
    categories.value.push({
      categoryName: newCategoryName.value,
      customers: [],
    });
    newCategoryName.value = '';
    updateLocalStorage();
  }
}

function addCustomer(categoryIndex) {
  if (newCustomerName.value.trim() !== '' && newCustomerAmount.value.trim() !== '') {
    categories.value[categoryIndex].customers.push({
      name: newCustomerName.value,
      amount: Number(newCustomerAmount.value),
      description: newCustomerDescription.value,
    });
    newCustomerName.value = '';
    newCustomerAmount.value = '';
    newCustomerDescription.value = '';
    updateLocalStorage();
  }
}

function removeCategory(categoryIndex) {
  categories.value.splice(categoryIndex, 1);
  updateLocalStorage();
}

function removeCustomer(categoryIndex, customerIndex) {
  categories.value[categoryIndex].customers.splice(customerIndex, 1);
  updateLocalStorage();
}

function editCustomer(categoryIndex, customerIndex) {
  const customer = categories.value[categoryIndex].customers[customerIndex];
  const newName = prompt('تعديل اسم المشتري:', customer.name);
  const newAmount = prompt('تعديل السعر:', customer.amount);
  const newDescription = prompt('تعديل الوصف:', customer.description);

  if (newName !== null) customer.name = newName;
  if (newAmount !== null) customer.amount = Number(newAmount);
  if (newDescription !== null) customer.description = newDescription;

  updateLocalStorage();
}

const totalCategories = computed(() => {
  return categories.value.reduce((total, category) => {
    return total + category.customers.length;
  }, 0);
});

const totalAmount = computed(() => {
  return categories.value.reduce((sum, category) => {
    return (
      sum +
      category.customers.reduce((customerSum, customer) => {
        return customerSum + parseInt(customer.amount);
      }, 0)
    );
  }, 0);
});

function updateLocalStorage() {
  localStorage.setItem('categories', JSON.stringify(categories.value));
}
</script>

<style scoped>
.v-btn {
  padding: 4px;
}
.ul-display,
.ul-display li {
  width: 100% !important;
}
.v-btn {
  margin-right: 10px;
}
.d-f-wor,
.d-f-wor2 {
  display: flex;
  align-items: center;
}
</style>
