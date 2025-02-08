<template>
  <v-container>
    <!-- Success Alert -->
    <v-alert v-if="successMessage" type="success" dismissible class="mt-3 mb-5">
      {{ successMessage }}
    </v-alert>

    <!-- Add/Edit School Form -->
    <v-form @submit.prevent="submitSchool" class="d-flex">
      <VTextField v-model="school.name" :label="$t('School Name')" required></VTextField>
      <VTextField v-model="school.address" :label="$t('Address')" required></VTextField>
      <VTextField v-model="school.phone" :label="$t('Phone')"></VTextField>
      <!-- <VTextField v-model="school.email" :label="$t('Email')" required></VTextField> -->
      <v-btn type="submit" color="success">
        {{ isEditing ? $t('Update School') : $t('Add School') }}
      </v-btn>
    </v-form>

    <!-- Schools Table -->
    <v-data-table
      :headers="headers"
      :items="schools"
      item-value="id"
      class="mt-5"
      dense
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title>{{ $t('Schools') }}</v-toolbar-title>
        </v-toolbar>
      </template>

      <template #item.actions="{ item }">
        <v-btn color="primary" small @click="editSchool(item)">{{ $t('Edit') }}</v-btn>
        <v-btn color="error" small @click="deleteSchool(item.id)">{{ $t('Delete') }}</v-btn>
      </template>
    </v-data-table>
  </v-container>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      school: {
        name: "",
        address: "",
        phone: "",
        //email: "",
      },
      schools: [],
      successMessage: "",
      headers: [
        { title: "ID", value: "id" },
        { title: "Name", value: "name" },
        { title: "Address", value: "address" },
        { title: "Phone", value: "phone" },
        // { title: "Email", value: "email" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      isEditing: false,
      editingSchoolId: null,
    };
  },
  methods: {
    // Fetch schools
    async fetchSchools() {
      try {
        const response = await axios.get("/api/schools");
        this.schools = response.data.data;
      } catch (error) {
        console.error("Error fetching schools:", error.response.data);
      }
    },

    // Add or update school
    async submitSchool() {
      try {
        if (this.isEditing) {
          await axios.put(`/api/schools/${this.editingSchoolId}`, this.school);
          this.successMessage = "School updated successfully!";
        } else {
          await axios.post("/api/schools", this.school);
          this.successMessage = "School added successfully!";
        }
        this.resetForm();
        this.fetchSchools();
      } catch (error) {
        console.error("Error submitting school:", error.response.data);
      }
    },

    // Edit school
    editSchool(item) {
      this.isEditing = true;
      this.editingSchoolId = item.id;
      this.school = { ...item };
    },

    // Delete school
    async deleteSchool(id) {
      try {
        await axios.delete(`/api/schools/${id}`);
        this.successMessage = "School deleted successfully!";
        this.fetchSchools();
      } catch (error) {
        console.error("Error deleting school:", error.response.data);
      }
    },

    // Reset form
    resetForm() {
      this.school = {
        name: "",
        address: "",
        phone: "",
        // email: "",
      };
      this.isEditing = false;
      this.editingSchoolId = null;
    },
  },
  mounted() {
    this.fetchSchools();
  },
};
</script>
