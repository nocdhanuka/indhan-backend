package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey
import androidx.room.TypeConverters
import com.dhanuka.indhan.roomdatabase.convertors.CircularContactConvertor
import com.dhanuka.indhan.roomdatabase.convertors.CircularDataConvertor

data class CircularListResponse(
    val base_url: String,
    val crr_date: String,
    val `data`: List<CircularListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class CircularListData(
    @TypeConverters(CircularContactConvertor::class)
    val circular_contacts: List<CircularContact>,
    @TypeConverters(CircularDataConvertor::class)
    val circular_data: List<CircularData>,
    @PrimaryKey
    val circular_id: Int,
    val circular_name: String?,
    val circular_path: String?,
    val created_at: String?,
    val institutionalType: String?,
    val published_date: String?,
    val status: Int?,
    val state_id: String?,
    val district_id: String?,
    val state: String?,
    val district: String?,
    val updated_at: String?,
    val language_name: String?,
    val language_id: String?,
    val user_ecn: String?,
    val user_id: Int?,
    val user_name: String?,
    val user_type: String?,
    val whatsapp_message: String?
)

data class CircularContact(
    val created_at: String?,
    val designation: String?,
    val email: String?,
    val level: Int?,
    val mobile_number: String?,
    val name: String?,
    val status: Int?,
    val tea_estate_contact_id: Int?,
    val tea_estate_id: Int?,
    val updated_at: String?,
    val wa_number: String?
)

data class CircularData(
    val can_be_shared: String?,
    val contact_designation: String?,
    val contact_name: String?,
    val contact_number: String?,
    val created_at: String?,
    val date_time: String?,
    val description: String?,
    val estate_testimonial_id: Int?,
    val image_video: String?,
    val latitude: String?,
    val testimonial_name: String?,
    val longitude: String?,

    val status: Int?,
    val tea_estate_contact_id: Int?,
    val tea_estate_id: Int?,
    val updated_at: String?,
    val wa_contact_number: String?
)