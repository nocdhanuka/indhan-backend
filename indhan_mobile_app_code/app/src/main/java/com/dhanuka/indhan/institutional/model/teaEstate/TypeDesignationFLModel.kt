package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class TypeDesignationFLModel(
    val crr_date: String,
    val `data`: Data,
    val message: String,
    val status: String,
    val status_code: Int
)

data class Data(
    val category_data: List<CategoryData>,
    val company_data: List<CompanyTypeDataList>,
    val designation_data: List<DesignationDataList>,
    val fl_data: List<FlDataList>
)

@Entity
data class CompanyTypeDataList(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val company_type: String?,
    val estate_company_id: Int?
)

@Entity
data class DesignationDataList(
    @PrimaryKey(autoGenerate = true)
    val _id: Int,
    val designation: String?,
    val estate_designation_id: Int?,
    val level: String?
)

@Entity
data class FlDataList(
    @PrimaryKey
    val ECN: String,
    val id: Int,
    val manager_id: String?,
    val manager_name: String?,
    val name: String?
)

@Entity
data class CategoryData(
    @PrimaryKey
    val estate_category_id: Int,
    val company_category: String?
)