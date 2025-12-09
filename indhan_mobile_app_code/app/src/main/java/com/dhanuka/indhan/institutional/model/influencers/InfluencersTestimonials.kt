package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

data class InfluencersTestimonials(
    val base_url: String,
    val crr_date: String,
    val `data`: List<InfluencerTestimonialData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class InfluencerTestimonialData(
    val created_at: String,
    val crop_id: String,
    val crop_ids: String,
    val description: String,
    val farmer: String,
    val mobile: String,
    val product_id: String,
    val product_ids: String,
    val rating: String,
    val status: String,
    val testimonial: String,
    @PrimaryKey
    val testimonial_id: String,
    val uploaded_file: String
)