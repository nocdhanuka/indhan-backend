package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class ContactListResponse(
    val crr_date: String,
    val `data`: List<ContactTeaEstate>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class ContactTeaEstate(
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
    val tea_estate_contact_id: Int,
    val tea_estate_id: Int,
    val updated_at: String,
    val wa_number: String
)