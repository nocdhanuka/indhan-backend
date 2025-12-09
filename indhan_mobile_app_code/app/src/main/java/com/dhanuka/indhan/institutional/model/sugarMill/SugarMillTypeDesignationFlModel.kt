package com.dhanuka.indhan.institutional.model.sugarMill

import androidx.room.Entity
import androidx.room.PrimaryKey

data class SugarMillTypeDesignationFlModel(
    val crr_date: String,
    val `data`: Data,
    val message: String,
    val status: String,
    val status_code: Int
)

data class Data(
    val category_data: List<SugarMillCategoryData>,
    val company_data: List<SugarMillCompanyData>,
    val designation_data: List<SugarMillDesignationData>,
    val fl_data: List<SugarMillFlData>
)

@Entity
data class SugarMillCategoryData(
    val company_category: String,
    @PrimaryKey
    val sugarmill_category_id: Int
)

@Entity
data class SugarMillCompanyData(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val company_type: String,
    val sugarmill_company_id: Int
)

@Entity
data class SugarMillDesignationData(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val designation: String,
    val level: String,
    val sugarmill_designation_id: Int
)

@Entity
data class SugarMillFlData(
    @PrimaryKey
    val ECN: String,
    val manager_id: String,
    val id: Int,
    val manager_name: String,
    val name: String
)