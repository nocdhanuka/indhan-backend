package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

data class ChatListResponse(
    val crr_date: String,
    val `data`: List<ChatListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class ChatListData(
    val content: String,
    val created_at: String,
    val demo_material_request_id: Int,
    @PrimaryKey()
    val demo_material_request_qa_id: Int,
    val updated_at: String,
    val user_ecn: String,
    val user_id: Int,
    val user_name: String,
    val user_type: String
)