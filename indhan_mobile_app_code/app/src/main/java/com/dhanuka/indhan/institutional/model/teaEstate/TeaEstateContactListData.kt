package com.dhanuka.indhan.institutional.model.teaEstate

data class TeaEstateContactListData(
    val crr_date: String,
    val `data`: List<TeaEstateContact>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class TeaEstateContact(
    val created_at: String,
    val designation: String,
    val email: String,
    val level: Int,
    val mobile_number: String,
    val name: String,
    val status: Int,
    val tea_estate_contact_id: Int,
    val tea_estate_id: Int,
    val updated_at: String,
    val wa_number: String
)