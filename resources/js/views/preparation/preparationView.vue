<template>
  <div>
    <!-- New Preparation -->
    <addPreparationComponent />
    <!-- New Type Preparation -->
    <v-btn class="m-2" color="red" text @click="dialogAddTypePreparation = true"
      >اضافة نوع تحضيرة
    </v-btn>
    <v-dialog v-model="dialogAddTypePreparation">
      <v-card class="p-3">
        <v-card-title class="text-center m-4"> اضافة نوع تحضيرة </v-card-title>
        <v-text-field
          type="text"
          variant="outlined"
          label=" اسم نوع التحضيرة "
          v-model="newTypePreparation.name"
          :rules="[v => !!v || 'This field is required']"
        >
        </v-text-field>
        <v-text-field
          type="text"
          variant="outlined"
          label=" وصف نوع التحضيرة "
          v-model="newTypePreparation.description"
          :rules="[v => !!v || 'This field is required']"
        >
        </v-text-field>
        <v-select
          label=" اختر المحطة "
          :items="stations"
          item-title="name"
          item-value="id"
          auto-select-first
          v-model="newTypePreparation.station_id"
        ></v-select>
        <div class="mx-auto">
          <v-btn class="mx-3" color="green" text @click="addTypePreparation()">{{
            $t('yes')
          }}</v-btn>
          <v-btn class="mx-3" color="red" text @click="dialogAddTypePreparation = false">{{
            $t('no')
          }}</v-btn>
        </div>
      </v-card>
    </v-dialog>
    <div v-if="!store.overlay">
      <!-- strt -->
      <v-expansion-panels>
        <v-expansion-panel
          variant="popout"
          v-for="typePrep in typePreparationAll"
          :key="typePrep.id"
        >
          <v-expansion-panel-title>{{ typePrep.name }}</v-expansion-panel-title>
          <v-expansion-panel-text>
            <v-table height="300px" fixed-header>
              <thead class="preparationTable">
                <tr>
                  <th class="text-left">وقت التحطيرة</th>
                  <th class="text-left">طن الشرائح</th>
                  <th class="text-left">الوردية</th>
                  <th class="text-left">الكمية</th>
                  <th class="text-left">عدد الساعات</th>
                  <th class="text-left">ppm</th>
                  <th class="text-left">بواسطة</th>
                </tr>
              </thead>
              <tbody class="preparationTableBoody">
                <tr v-for="preparation in typePrep.preparations" :key="preparation.id">
                  <td>{{ date(preparation.actual_time) }}</td>
                  <td>{{ preparation.slices_ton }}</td>
                  <td>{{ preparation.shift }}</td>
                  <td>{{ preparation.quantity }}</td>
                  <td>{{ preparation.cont_hours }}</td>
                  <td>{{ preparation.ppm }}</td>
                  <td>{{ preparation.user.name }}</td>
                </tr>
              </tbody>
            </v-table>
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>
      <!-- end -->
    </div>
  </div>
</template>

<script setup>
import addPreparationComponent from '../../components/preparation/addPreparationComponent.vue';
import { usemainStore } from '../../store/mainStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import moment from 'moment';
const store = usemainStore();
moment.locale('ar');
const newTypePreparation = ref({ user_id: `${store.user.id}` });
const dialogAddTypePreparation = ref(false);
const stations = ref('');
const typePreparationAll = ref('');
onMounted(() => {
  store.overlay = true;
  axios
    .get(`Stations?current_user=${store.user.id}`)
    .then(res => {
      stations.value = res.data;
      newTypePreparation.value.station_id = stations.value[0].id;
    })
    .catch(() => {
      store.startSnack('error', 'no', 'danger');
    });
  axios.get(`typePre?relation=preparations.user`).then(res => {
    // console.log(store.preparations);
    typePreparationAll.value = res.data;
    console.log(typePreparationAll.value);
    store.overlay = false;
  });
});
function timeSinceReport(time) {
  return moment(time).fromNow();
}
function date(d) {
  return moment(d).format('dddd :- h:mm A - MM/DD ');
}
function addTypePreparation() {
  console.log(newTypePreparation.value);
  axios
    .post(`typePre`, newTypePreparation.value)
    .then(() => {
      newTypePreparation.value = '';
      dialogAddTypePreparation.value = false;
      store.getTypePre('preparations.user');
    })
    .catch(() => {
      store.startSnack('error', 'no', 'danger');
    });
}
</script>

<style scoped lang="scss">
.preparationTableBoody {
  tr {
    &:hover {
      td {
        background-color: blue!important;
      }
    }
  }
}


.v-table__wrapper td,
.v-table__wrapper th {
  white-space: nowrap;
  text-align: right !important;
}
</style>
