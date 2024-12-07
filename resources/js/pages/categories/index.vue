<template>
  <v-container>
    <!-- Success Alert -->
    <v-alert
      v-if="successMessage"
      type="success"
      dismissible
      class="mt-3 mb-5"
    >
      {{ successMessage }}
    </v-alert>

    <!-- Add Category Form -->
    <v-form @submit.prevent="createCategory">
      <v-text-field
        v-model="category.name"
        :label="$t('Category Name')"
        class="mb-5"
        required
      ></v-text-field>
      <v-btn type="submit" color="success">{{ $t('Add Category') }}</v-btn>
    </v-form>

    <!-- Categories Table -->
    <v-data-table
      :headers="headers"
      :items="categories"
      item-value="id"
      class="mt-5"
      dense
    >
      <template #top>
        <v-toolbar flat>
          <v-toolbar-title>{{ $t('Categories') }}</v-toolbar-title>
        </v-toolbar>
      </template>

      <template #item.actions="{ item }">
        <v-btn color="primary" small @click="editCategory(item)">{{ $t('Edit') }}</v-btn>
        <v-btn color="error" small @click="deleteCategory(item.id)">{{ $t('Delete') }}</v-btn>
      </template>
    </v-data-table>
  </v-container>
</template>


<script>
import axios from "axios";

export default {
  data() {
    return {
      category: {
        name: "",
      },
      categories: [],
      successMessage: "",
      headers: [
        { title: "ID", value: "id" },
        { title: "Name", value: "name" },
        { title: "Actions", value: "actions", sortable: false },
      ],
      editingCategory: null, // Used for edit functionality
    };
  },
  methods: {
    // Fetch all categories
    async fetchCategories() {
      try {
        const response = await axios.get("/api/categories");
        this.categories = response.data.data;
      } catch (error) {
        console.error("Error fetching categories:", error.response.data);
      }
    },

    // Add a new category
    async createCategory() {
      try {
        if (this.editingCategory) {
          // Update category if in editing mode
          const response = await axios.put(
            `/api/categories/${this.editingCategory.id}`,
            this.category
          );
          this.successMessage = "Category updated successfully!";
          this.editingCategory = null; // Clear editing mode
        } else {
          // Create new category
          const response = await axios.post("/api/categories", this.category);
          this.successMessage = "Category added successfully!";
        }
        this.category.name = ""; // Clear input field
        this.fetchCategories(); // Refresh table
      } catch (error) {
        console.error("Error adding/updating category:", error.response.data);
      }
    },

    // Edit a category
    editCategory(item) {
      this.editingCategory = item;
      this.category.name = item.name;
    },

    // Delete a category
    async deleteCategory(id) {
      try {
        await axios.delete(`/api/categories/${id}`);
        this.successMessage = "Category deleted successfully!";
        this.fetchCategories(); // Refresh table
      } catch (error) {
        console.error("Error deleting category:", error.response.data);
      }
    },
  },
  mounted() {
    this.fetchCategories(); // Fetch categories when component is mounted
  },
};
</script>
