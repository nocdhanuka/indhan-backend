package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.DemoRequestEvaluated.models.ChatListData

@Dao
interface ChatListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: List<ChatListData>)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ChatListData)

    @Query("SELECT * FROM ChatListData")
    fun getAllMessagesData(): List<ChatListData?>?


    @Query("SELECT * FROM ChatListData WHERE demo_material_request_id =:demo_id")
    fun getAllMessagesData(demo_id: Int): List<ChatListData?>?

    @Query("DELETE FROM ChatListData WHERE demo_material_request_qa_id =:id")
    fun deleteTableData(id: Int)
}