package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.ContactTeaEstate

@Dao
interface ContactTeaEstateDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ContactTeaEstate)

    @Query("SELECT * FROM ContactTeaEstate")
    fun getAllContactListData(): List<ContactTeaEstate?>?

    @Query("SELECT * FROM ContactTeaEstate WHERE tea_estate_id =:id")
    fun getAllContactById(id: Int): List<ContactTeaEstate?>?
}