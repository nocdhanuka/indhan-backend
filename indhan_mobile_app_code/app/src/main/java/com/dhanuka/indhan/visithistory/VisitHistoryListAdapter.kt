package com.dhanuka.indhan.visithistory

import android.content.Context
import androidx.databinding.DataBindingUtil
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.ViewGroup
import com.bumptech.glide.Glide
import com.dhanuka.indhan.R
import com.dhanuka.indhan.databinding.ItemVisitHistoryBinding


class VisitHistoryListAdapter(
    var context: Context,
    var list: MutableList<VisitHistoryFarmerBean.Data>
) : RecyclerView.Adapter<VisitHistoryListAdapter.ViewHolder>() {

    var layoutInflater = LayoutInflater.from(context)
    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding: ItemVisitHistoryBinding =
            DataBindingUtil.inflate(layoutInflater, R.layout.item_visit_history, parent, false)
        return ViewHolder(binding)
    }

    override fun getItemCount(): Int {
        return list.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {

        holder.setData(position)


    }


    inner class ViewHolder(var view: ItemVisitHistoryBinding) : RecyclerView.ViewHolder(view.root) {
        // Holds the TextView that will add each animal to
        fun setData(position: Int) {

            view.txtVillageName.text = list[position].village
            view.txtDateValue.text = list[position].createdAt

//            view.imgTestimonial.visibility= View.GONE

//            //view.imgTestimonial.setImageBitmap(retriveVideoFrameFromVideo(list[position].uploadedFile))
//
//
            Glide.with(context)
                .asBitmap()
                .load(list[position].visitImage)
//                    .centerCrop()
//                    .crossFade()
                .into(view.imgFarmer)
        }
//
//            view.imgTestimonial.setOnClickListener {
//                val intent = Intent(context, WebViewActivity::class.java)
//                intent.putExtra("key",list[position].uploadedFile)
//                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//                context.startActivity(intent)
//            }//
//            view.llayout.setOnClickListener {
//                val intent = Intent(context, FarmerDetailsActivity::class.java)
//                intent.putExtra("data",list[position])
//                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
//                context.startActivity(intent)
//            }

    }


}