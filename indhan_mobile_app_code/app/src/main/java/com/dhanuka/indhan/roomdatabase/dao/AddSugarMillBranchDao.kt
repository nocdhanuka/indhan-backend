package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarMill.AddSugarMillBranchModel

@Dao
interface AddSugarMillBranchDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: AddSugarMillBranchModel?): Long

    @Query("SELECT * FROM AddSugarMillBranchModel WHERE status =:status LIMIT 1")
    fun getSingleAddSugarMillBranchData(
        status: Int
    ): AddSugarMillBranchModel?

    @Query("SELECT * FROM AddSugarMillBranchModel")
    fun getAllAddSugarMillBranchData(): List<AddSugarMillBranchModel>?

    @Query("SELECT * FROM AddSugarMillBranchModel WHERE status =:status AND hitTime<=:time LIMIT 1")
    fun getSingleAddSugarMillBranchData(status: Int, time: Long): AddSugarMillBranchModel?

    @Query("SELECT * FROM AddSugarMillBranchModel WHERE hitTime<=:time LIMIT 1")
    fun getSosAddSugarMillBranchData(time: Long): AddSugarMillBranchModel?

    @Query("UPDATE AddSugarMillBranchModel SET status =:status ,hitTime =:lastHitTime WHERE sugarmillId=:id")
    fun updateAddSugarMillBranchData(id: Long, lastHitTime: Long, status: Int)

    @Query("DELETE FROM AddSugarMillBranchModel WHERE sugarmillId =:id")
    fun deleteAddSugarMillBranchData(id: Long)

}