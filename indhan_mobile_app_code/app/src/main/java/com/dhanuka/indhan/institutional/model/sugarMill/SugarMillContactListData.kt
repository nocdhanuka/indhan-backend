package com.dhanuka.indhan.institutional.model.sugarMill

data class SugarMillContactListData(
    val crr_date: String,
    val `data`: List<SugarMillContact>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class SugarMillContact(
    val sugarmill_contact_id: Int,
    val created_at: String,
    val designation: String,
    val email: String,
    val level: Int,
    val mobile_number: String,
    val name: String,
    val status: Int,
    val branch_id: Int,
    val sugarmill_id: Int,
    val updated_at: String,
    val wa_number: String
)