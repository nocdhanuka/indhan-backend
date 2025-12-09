package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.ContactSugarMill

@Dao
interface ContactSugarMillDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ContactSugarMill)

    @Query("SELECT * FROM ContactSugarMill")
    fun getAllContactListData(): List<ContactSugarMill?>?

    @Query("SELECT * FROM ContactSugarMill WHERE sugarmill_id =:id")
    fun getAllContactById(id: Int): List<ContactSugarMill?>?
}