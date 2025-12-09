package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class SugarMillContactListResponse(
    val crr_date: String,
    val `data`: List<ContactSugarMill>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class ContactSugarMill(
    val created_at: String,
    val designation: String,
    val email: String,
    val level: Int,
    val mobile_number: String,
    val name: String,
    val status: Int,
    val state_id: String?,
    val district_id: String?,
    @PrimaryKey
    val sugarmill_contact_id: Int,
    val sugarmill_id: Int,
    val updated_at: String,
    val wa_number: String
)