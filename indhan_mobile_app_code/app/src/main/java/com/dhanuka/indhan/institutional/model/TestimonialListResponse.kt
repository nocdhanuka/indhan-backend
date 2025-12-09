package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class TestimonialListResponse(
    val `data`: List<TestimonialListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class TestimonialListData(
    val can_be_shared: String?,
    val contact_designation: String?,
    val contact_name: String?,
    val contact_number: String?,
    val created_at: String?,
    val date_time: String?,
    val description: String?,
    @PrimaryKey
    val institutional_testimonial_id: Int,
    val image_video: String?,
    val latitude: String?,
    val longitude: String?,
    val testimonial_name: String?,
    val language_id: String?,
    val language_name: String?,
    val status: Int?,
    val institutional_contact_id: Int?,
    val institutional_id: Int?,
    val institutional_name: String,
    val institutional_office_type: String?,
    val institutionalType: String?,
    val type: String?,
    val updated_at: String?,
    val user_ecn: String?,
    val wa_contact_number: String?
)