package com.dhanuka.indhan.institutional.model.influencers

data class InfluencersContactListData(
    val crr_date: String,
    val `data`: List<InfluencersContact>,
    val message: String,
    val status: String,
    val status_code: Int
)

data class InfluencersContact(
    val influencer_contact_id: Int,
    val created_at: String,
    val designation: String,
    val email: String,
    val level: Int,
    val mobile_number: String,
    val name: String,
    val status: Int,
    val branch_id: Int,
    val influencer_id: Int,
    val updated_at: String,
    val wa_number: String
)