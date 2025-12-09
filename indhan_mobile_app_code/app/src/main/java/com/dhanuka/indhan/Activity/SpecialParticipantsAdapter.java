package com.dhanuka.indhan.Activity;//package com.dhanuka.indhan.Activity.RegisterFarmersMVP.MissCallMVP;

import android.content.Context;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;

import com.dhanuka.indhan.Fonts.Regular;
import com.dhanuka.indhan.Fonts.RegularEditText;
import com.dhanuka.indhan.Model.UserModel;
import com.dhanuka.indhan.R;

import java.util.ArrayList;


public class SpecialParticipantsAdapter extends RecyclerView.Adapter<SpecialParticipantsAdapter.MyViewHolder> {

    Context mContext;
    private ArrayList<UserModel> selectedLogValues;

    public SpecialParticipantsAdapter(Context context, ArrayList<UserModel> selectedLogValues) {
        this.mContext = context;
        this.selectedLogValues = selectedLogValues;
    }

    @NonNull
    @Override
    public MyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View itemView = LayoutInflater.from(parent.getContext()).inflate(R.layout.special_user_item, parent, false);
        return new SpecialParticipantsAdapter.MyViewHolder(itemView);
    }

    @Override
    public void onBindViewHolder(@NonNull MyViewHolder holder, int position) {
        if (position == 0) {
        }
        UserModel model = selectedLogValues.get(position);
        holder.etName.setText(model.getName());
        holder.etPhone.setText(model.getNumber());
    }

    @Override
    public int getItemCount() {
        return selectedLogValues.size();
    }

    class MyViewHolder extends RecyclerView.ViewHolder {
        private RegularEditText etName, etPhone;
        private Regular tvDelete;

        MyViewHolder(View view) {
            super(view);
            etName = view.findViewById(R.id.etName);
            tvDelete = view.findViewById(R.id.tvDelete);
            etPhone = view.findViewById(R.id.etPhone);

            tvDelete.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Log.e("vinod", "adapter delete clicked");
                }
            });
        }
    }
}
