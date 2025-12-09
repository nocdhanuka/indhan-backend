package com.dhanuka.indhan.institutional.model.teaEstate

import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.TypeConverters
import com.dhanuka.indhan.roomdatabase.convertors.ContactConvertor

data class TeaEstateListResponse(
    val base_url: String,
    val crr_date: String,
    val `data`: List<TeasEstateListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class TeasEstateListData(
    val address: String?,
    val company_category: String?,
    @TypeConverters(ContactConvertor::class)
    val contact_data: List<ContactListData>,
    val created_at: String?,
    val district: String?,
    val district_id: Int?,
    val estate_company_id: Int?,
    val fl_ecn: String?,
    val fl_name: String?,
    val fl_type: String?,
    val image: String?,
    val land_area: String?,
    val latitude: String?,
    val longitude: String?,
    val name: String?,
    val office_type: String?,
    val office_type_name: String?,
    val parent_tea_estate_id: Int,
    val state: String?,
    val state_id: Int?,
    val status: Int?,
    @PrimaryKey
    val tea_estate_id: Int,
    val tea_estate_type: String?,
    val tea_estate_type_name: String?,
    val turn_over: String?,
    val updated_at: String?
)

data class ContactListData(
    val created_at: String,
    val designation: String,
    val email: String,
    val level: Int,
    val mobile_number: String,
    val name: String,
    val status: Int,
    val branch_id: Int,
    val tea_estate_contact_id: Int,
    val tea_estate_id: Int,
    val updated_at: String,
    val wa_number: String
)
