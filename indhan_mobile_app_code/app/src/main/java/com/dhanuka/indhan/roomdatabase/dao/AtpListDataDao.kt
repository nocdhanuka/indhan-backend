package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.teaEstate.AtpListData

@Dao
interface AtpListDataDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: AtpListData)

    @Query("SELECT * FROM AtpListData")
    fun getAllAtpListData(): List<AtpListData?>?

    @Query("SELECT * FROM AtpListData WHERE tea_estate_id =:id")
    fun getAllById(id: Int): List<AtpListData?>?

    @Query("DELETE FROM AtpListData WHERE estate_atp_id =:id")
    fun deleteTableData(id: Int)

}